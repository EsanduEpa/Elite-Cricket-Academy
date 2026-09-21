from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC

from pages.base_page import BasePage


class LoginPage(BasePage):
    EMAIL = (By.ID, "email")
    PASSWORD = (By.ID, "password")
    SUBMIT = (By.CSS_SELECTOR, "button.login-btn[type='submit']")
    ERROR = (By.CSS_SELECTOR, ".error-message.show")

    def open(self) -> None:
        self.driver.get(f"{self.base_url}/login")
        self.wait.until(EC.visibility_of_element_located(self.EMAIL))

    def login(self, username: str, password: str) -> None:
        self.wait.until(EC.element_to_be_clickable(self.EMAIL)).clear()
        self.driver.find_element(*self.EMAIL).send_keys(username)
        self.driver.find_element(*self.PASSWORD).send_keys(password)
        self.driver.find_element(*self.SUBMIT).click()

    def logout(self) -> None:
        self.driver.get(f"{self.base_url}/login/logout")
