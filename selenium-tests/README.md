# Elite Cricket Academy Selenium Test Automation

Python, Selenium WebDriver, and Pytest browser regression coverage for the existing Elite Cricket Academy PHP/MySQL application. The test suite is intentionally separate from application code.

## Coverage

| ID | Objective | Preconditions / test data | Expected assertion |
| --- | --- | --- | --- |
| AUTH-001 | Display the login form | Application available | Login page and email field are visible |
| AUTH-002 | Validate required credentials | Logged out | Browser prevents an empty submission |
| AUTH-003 | Reject invalid credentials | Logged out | Existing server error is displayed |
| AUTH-004 | Authenticate an Admin | Configured Admin account | Redirects to `/admin/dashboard` |
| AUTH-005 | Log out | Authenticated Admin | Login form returns after session destruction |
| AUTH-006 | Protect Admin Player Management | Logged out | Route redirects to the public home page |
| PLAYER-007 | Reject an underage player | Authenticated Admin | Existing create endpoint returns the age-validation alert |
| PLAYER-008 | Create, search, update, and delete a player | Authenticated Admin; generated `selenium_...` user | Each UI result is asserted; only the generated player is deleted |

The CRUD workflow uses a timestamped `@example.test` email and performs best-effort cleanup in `finally`. It never selects or removes existing users.

## Structure

```
selenium-tests/
  pages/                 # Page Object Model: locators and reusable UI actions
  tests/                 # Scenario-focused pytest files and assertions
  conftest.py            # Chrome, URL, and credentials fixtures
  requirements.txt       # Python dependencies
  pytest.ini             # Collection and markers
```

## Setup

1. Start Apache and MySQL through XAMPP, import/configure the `cricket_academy` database, and open `http://localhost/Elite/public` once to confirm the application is available.
2. From this directory, install dependencies:

   ```powershell
   py -m pip install -r requirements.txt
   ```

3. Set test credentials if the default seeded Admin account is unavailable:

   ```powershell
   $env:ELITE_ADMIN_USERNAME = "your-admin-username"
   $env:ELITE_ADMIN_PASSWORD = "your-admin-password"
   ```

`ELITE_BASE_URL` can override `http://localhost/Elite/public`, and `ELITE_CHROMEDRIVER` can point to a local matching ChromeDriver. Selenium Manager is used automatically when no driver path is supplied.

## Run

```powershell
py -m pytest -v --html=test-results.html --self-contained-html
```

Use `--headed` to watch Chrome. The HTML report is written as `test-results.html` and is ignored by Git.

## Architecture

Tests keep scenarios and assertions separate from page behavior. `LoginPage` and `AdminPlayersPage` expose existing reliable IDs and CSS selectors. The suite uses explicit `WebDriverWait` conditions rather than fixed sleeps, and covers positive, negative, role/access, functional, and regression paths.

## Latest execution

The latest local execution result is recorded after each verification run. See the final project delivery note for the current pass/fail count.
