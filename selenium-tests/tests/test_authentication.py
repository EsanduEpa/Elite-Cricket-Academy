import pytest
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait

from pages.login_page import LoginPage


@pytest.mark.smoke
def test_auth_001_login_page_is_available(driver, base_url):
    page = LoginPage(driver, base_url)
    page.open()
    assert "Login" in driver.page_source
    assert driver.find_element(By.ID, "email").is_displayed()


@pytest.mark.smoke
def test_auth_002_required_login_fields_use_browser_validation(driver, base_url):
    page = LoginPage(driver, base_url)
    page.open()
    driver.find_element(*page.SUBMIT).click()
    assert driver.execute_script("return document.getElementById('email').validationMessage")
    assert driver.current_url.endswith("/login")


@pytest.mark.smoke
def test_auth_003_invalid_credentials_show_server_error(driver, base_url):
    page = LoginPage(driver, base_url)
    page.open()
    page.login("not-a-real-user", "wrong-password")
    error = WebDriverWait(driver, 12).until(EC.visibility_of_element_located(page.ERROR))
    assert "Invalid email/username or password" in error.text


@pytest.mark.smoke
def test_auth_004_valid_admin_login_redirects_to_dashboard(driver, base_url, admin_credentials):
    page = LoginPage(driver, base_url)
    page.open()
    page.login(*admin_credentials)
    WebDriverWait(driver, 12).until(EC.url_contains("/admin/dashboard"))
    assert "/admin/dashboard" in driver.current_url


@pytest.mark.smoke
def test_auth_005_logout_ends_authenticated_session(driver, base_url, admin_credentials):
    page = LoginPage(driver, base_url)
    page.open()
    page.login(*admin_credentials)
    WebDriverWait(driver, 12).until(EC.url_contains("/admin/dashboard"))
    page.logout()
    WebDriverWait(driver, 12).until(EC.url_matches(r".*/Elite/?$"))
    driver.get(f"{base_url}/admin/players")
    WebDriverWait(driver, 12).until(EC.url_matches(r".*/Elite/?$"))
    assert "/admin/players" not in driver.current_url


@pytest.mark.regression
def test_auth_006_unauthenticated_admin_route_redirects_to_public_page(driver, base_url):
    driver.get(f"{base_url}/admin/players")
    WebDriverWait(driver, 12).until(EC.url_matches(r".*/Elite/?$"))
    assert "/admin/players" not in driver.current_url
