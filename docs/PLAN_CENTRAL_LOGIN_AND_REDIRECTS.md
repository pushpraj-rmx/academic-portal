# Plan: Central Login at /login with Role-Based Redirects

**Goal:** `/login` is the single entry point. After login, redirect by user type: **Student → /student**, **everyone else (admin roles) → /admin**.

---

## 1. Current State

- GET `/login` redirects to Filament `/admin` login (no form at `/login`).
- POST `/login` does not exist (removed).
- Logout redirects to Filament login.
- Filament panel has `->login()` (its own login page at `/admin/login`).

---

## 2. Target State

| Route    | Purpose |
|----------|--------|
| GET `/login`  | Show login form (Breeze-style). |
| POST `/login` | Authenticate; then redirect by role (see below). |
| GET `/student` | Student dashboard (auth required; Student role). |
| GET `/admin`   | Filament panel (auth required; non-Student roles). |
| Logout         | Redirect to `/login`. |

**Redirect after successful login:**

- If user has **only** the **Student** role → `redirect()->route('student.dashboard')` (i.e. `/student`).
- Otherwise (SuperAdmin, AcademicAdmin, ExaminationCell, PlacementCell, ContentManager, or any non-Student role) → `redirect()->route('filament.admin.pages.dashboard')` (i.e. `/admin`).
- If user has **no roles** → redirect to `/login` or a generic dashboard; recommend redirect to `/admin` and rely on Filament’s `canAccessPanel` (403) or redirect to `/login` with a message. **Recommendation:** redirect to `/admin`; Filament already denies access when `canAccessPanel` is false.

**Edge case (user has both Student and another role):** Treat as admin → redirect to `/admin`. So rule: redirect to `/student` only when the user has exactly the Student role; otherwise `/admin`.

---

## 3. Implementation Steps

### 3.1 Restore login form at `/login`

- **File:** `routes/auth.php`
  - Replace the current GET `login` closure (redirect to Filament) with:
    - `Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');`
  - Add:
    - `Route::post('login', [AuthenticatedSessionController::class, 'store']);`
- **File:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
  - `create()`: already returns `view('auth.login')` — keep as is (Breeze login view).
  - `store()`: after `$request->authenticate()` and `$request->session()->regenerate()`, replace the redirect with **role-based redirect** (see 3.2).
  - `destroy()`: change redirect from `route('filament.admin.auth.login')` to `route('login')`.

No new views required; use existing `resources/views/auth/login.blade.php` (form posts to `route('login')`).

### 3.2 Role-based redirect after login

- **File:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (in `store()`)
  - After authentication and session regenerate:
    - Get the authenticated user’s roles (e.g. `$user->getRoleNames()` or `$user->roles->pluck('name')`).
    - If the user has **exactly one** role and it is **Student** (compare to `UserRole::Student->value`), then:
      - `return redirect()->intended(route('student.dashboard'));`
    - Else:
      - `return redirect()->intended(route('filament.admin.pages.dashboard'));`
  - Use `redirect()->intended(...)` so that if the user had been sent to `/login` from a protected page (e.g. `/admin`), they are sent back there after login; otherwise use the default above.
  - Optional: extract this into a small helper or a dedicated class (e.g. `LoginRedirectService`) for clarity and testability.

### 3.3 Student dashboard route and view

- **Route:** In `routes/web.php`, inside the `middleware('auth')` group (or a dedicated group), add:
  - `Route::get('student', ...)->name('student.dashboard');`
  - Resolve via a closure that returns a view, or a minimal `StudentDashboardController@index` that returns `view('student.dashboard')`.
- **View:** Create `resources/views/student/dashboard.blade.php`.
  - Use a layout that matches the “app” area (e.g. `layouts.app` with navigation) so it feels consistent with the rest of the post-login app.
  - Content: simple placeholder (e.g. “Student dashboard”; “My results”, “My documents” etc. can be added later).
- **Access control:** Restrict `/student` to users with the Student role. Options:
  - Middleware that checks `auth()->user()->hasRole(UserRole::Student->value)` and redirects (e.g. to `/admin` or `/login`) if not, or
  - Controller/closure that does the same check and aborts or redirects.
  - Non-students visiting `/student` should get 403 or redirect to `/admin`; document the choice in DESIGN_DECISIONS.md.

### 3.4 Filament: remove its login and send guests to `/login`

- **File:** `app/Providers/Filament/AdminPanelProvider.php`
  - Remove `->login()` from the panel builder so Filament no longer registers its own login page.
- **Redirect when unauthenticated:** When a guest visits `/admin`, Filament’s `Authenticate` middleware calls `Filament::getLoginUrl()`. With `->login()` removed, `getLoginUrl()` returns `null`, which can break redirects. So:
  - **Option A (recommended):** Add a **custom middleware** that extends `Filament\Http\Middleware\Authenticate` and overrides `redirectTo($request)` to return `route('login')` (or `url('/login')`). Register this middleware in the panel’s `->authMiddleware([...])` instead of `Authenticate::class`. Then guests hitting `/admin` are sent to `/login`.
  - **Option B:** Keep `->login()` and replace the Filament Login page with a custom page that immediately redirects to `route('login')`. More invasive and duplicates the “login” concept; Option A is cleaner.

**Recommended for Option A:**

- Create `app/Http/Middleware/RedirectToCentralLogin.php` (or similar) that extends `Filament\Http\Middleware\Authenticate` and overrides `redirectTo()` to return `route('login')`.
- In `AdminPanelProvider`, set `->authMiddleware([\App\Http\Middleware\RedirectToCentralLogin::class])` (and register the middleware in `bootstrap/app.php` or as a route middleware alias if required by Filament).

### 3.5 Header and navigation

- **File:** `resources/views/components/public/header.blade.php`
  - “Login” link already points to `route('login')` — no change for guests.
  - For **authenticated** users:
    - If user has **Student** role (and no other role, or you decide “has student role”): show a “Student” or “My dashboard” link to `route('student.dashboard')`.
    - If user can access admin (e.g. has any role other than only Student): show “Admin” link to `/admin` (or `route('filament.admin.pages.dashboard')`).
  - Optional: show both “Student” and “Admin” when user has both roles; document the chosen behaviour in DESIGN_DECISIONS.md.
- **Dashboard link:** Current “Dashboard” in the header points to `url('/dashboard')`. Decide:
  - Either keep `/dashboard` as a generic page and leave the link as is, or
  - Make “Dashboard” role-aware (Student → `/student`, others → `/admin`). Plan recommends: keep `/dashboard` as a simple “You’re logged in” page; primary entry after login is already handled by the redirect in 3.2.

### 3.6 Tests

- **File:** `tests/Feature/Auth/AuthenticationTest.php`
  - Replace “login redirects to filament admin login” with: GET `/login` returns 200 and shows the login form (e.g. assertSee a form or “Log in”).
  - Replace “post login does not exist” with: POST `/login` with valid credentials authenticates and redirects (Student → `/student`, non-Student → `/admin`); assert intended redirect and `assertAuthenticated`.
  - Add (or keep) test: POST `/login` with invalid credentials does not authenticate and returns error (e.g. 302 back with errors).
  - Logout test: after logout, redirect to `route('login')` (not Filament login).
- **New:** `tests/Feature/StudentDashboardTest.php` (or similar): as Student, GET `/student` returns 200; as non-Student (e.g. AcademicAdmin), GET `/student` returns 403 or redirect. And optionally: guest GET `/student` redirects to `route('login')`.

### 3.7 DESIGN_DECISIONS.md

- Update **§ 1. Auth Entry Points** to state that the app now uses central login at `/login` with role-based redirects: Student → `/student`, others → `/admin`. Filament no longer has its own login; guests visiting `/admin` are redirected to `/login`.

---

## 4. Summary Checklist

| Task | Notes |
|------|--------|
| Restore GET/POST `login` in `routes/auth.php` | POST to `AuthenticatedSessionController@store`. |
| Role-based redirect in `store()` | Student-only → `/student`, else → `/admin`; use `intended()`. |
| Logout redirect to `route('login')` | In `AuthenticatedSessionController@destroy`. |
| Add route `GET /student` named `student.dashboard` | Auth + Student-only middleware or check. |
| Add view `student/dashboard.blade.php` | Placeholder; use app layout. |
| Remove `->login()` from Filament panel | In AdminPanelProvider. |
| Custom auth middleware for panel | Extend Filament Authenticate; `redirectTo()` → `route('login')`. |
| Header: Student link to `/student`, Admin link to `/admin` | When applicable by role. |
| Update auth tests | Login form at `/login`, POST redirects by role, logout → `/login`. |
| Optional: Student dashboard access test | Student 200, non-Student 403/redirect, guest → login. |
| Update DESIGN_DECISIONS.md | Central login and redirect behaviour. |

---

## 5. Optional / Later

- **Faculty dashboard:** When added, extend redirect logic (e.g. Faculty role → `/faculty`) and document in DESIGN_DECISIONS.md.
- **`/dashboard`:** Keep as generic or make it role-aware; avoid duplicate “home” semantics with `/student` and `/admin`.
- **LoginRedirectService:** Extract “where to send user after login” into a small service if the logic grows (e.g. more roles or tenant-based redirects).
