from __future__ import annotations

from dataclasses import dataclass
from datetime import date

from selenium.common.exceptions import TimeoutException
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select

from pages.base_page import BasePage


@dataclass(frozen=True)
class PlayerData:
    first_name: str
    last_name: str
    email: str
    username: str
    phone: str = "+94 771234567"
    dob: str = "2010-05-20"

    @property
    def full_name(self) -> str:
        return f"{self.first_name} {self.last_name}"


class AdminPlayersPage(BasePage):
    SEARCH = (By.ID, "playerSearch")
    ADD_BUTTON = (By.ID, "addPlayerBtn")
    NEXT_BUTTON = (By.ID, "playerWizardNextBtn")
    SUBMIT_BUTTON = (By.ID, "playerWizardSubmitBtn")
    ADD_MODAL = (By.ID, "addPlayerModal")
    EDIT_MODAL = (By.ID, "editPlayerModal")
    DELETE_MODAL = (By.ID, "deleteModal")

    def open(self) -> None:
        self.driver.get(f"{self.base_url}/admin/players")
        self.wait.until(EC.visibility_of_element_located(self.SEARCH))

    def create_player(self, player: PlayerData) -> None:
        self.wait.until(EC.element_to_be_clickable(self.ADD_BUTTON)).click()
        self.wait.until(EC.visibility_of_element_located(self.ADD_MODAL))
        values = {
            "playerFirstName": player.first_name,
            "playerLastName": player.last_name,
            "playerDOB": player.dob,
            "playerEmail": player.email,
            "playerPhone": player.phone,
            "playerUsername": player.username,
        }
        for field_id, value in values.items():
            element = self.driver.find_element(By.ID, field_id)
            if field_id == "playerDOB":
                self._set_date(element, value)
            else:
                element.clear()
                element.send_keys(value)
        Select(self.driver.find_element(By.ID, "playerSubscription")).select_by_value("basic")
        self.driver.find_element(*self.NEXT_BUTTON).click()
        self.wait.until(EC.element_to_be_clickable(self.SUBMIT_BUTTON)).click()
        self.wait.until(EC.alert_is_present()).accept()
        self.wait.until(EC.visibility_of_element_located(self.SEARCH))

    def assert_create_validation(self, player: PlayerData) -> str:
        self.driver.find_element(*self.ADD_BUTTON).click()
        self.wait.until(EC.visibility_of_element_located(self.ADD_MODAL))
        for field_id, value in {
            "playerFirstName": player.first_name,
            "playerLastName": player.last_name,
            "playerDOB": date.today().isoformat(),
            "playerEmail": player.email,
            "playerPhone": player.phone,
            "playerUsername": player.username,
        }.items():
            element = self.driver.find_element(By.ID, field_id)
            if field_id == "playerDOB":
                self._set_date(element, value)
            else:
                element.send_keys(value)
        Select(self.driver.find_element(By.ID, "playerSubscription")).select_by_value("basic")
        self.driver.find_element(*self.NEXT_BUTTON).click()
        self.wait.until(EC.element_to_be_clickable(self.SUBMIT_BUTTON)).click()
        return self.wait.until(EC.alert_is_present()).text

    def search(self, text: str) -> None:
        field = self.wait.until(EC.visibility_of_element_located(self.SEARCH))
        field.clear()
        field.send_keys(text)
        self.wait.until(lambda d: d.find_element(*self.SEARCH).get_attribute("value") == text)

    def player_action(self, email: str, action: str):
        row = self.wait.until(EC.presence_of_element_located((By.XPATH, f"//tr[.//td[normalize-space()={self._xpath_literal(email)}]]")))
        return row.find_element(By.CSS_SELECTOR, f"button.action-btn.{action}")

    def update_player_name(self, email: str, updated_first_name: str) -> None:
        self.player_action(email, "edit").click()
        self.wait.until(EC.visibility_of_element_located(self.EDIT_MODAL))
        first_name = self.driver.find_element(By.ID, "editPlayerFirstName")
        first_name.clear()
        first_name.send_keys(updated_first_name)
        self.driver.find_element(By.CSS_SELECTOR, "#editPlayerForm button[type='submit']").click()
        self.wait.until(EC.alert_is_present()).accept()
        self.wait.until(EC.visibility_of_element_located(self.SEARCH))

    def delete_player(self, email: str) -> None:
        self.search(email)
        self.player_action(email, "delete").click()
        self.wait.until(EC.visibility_of_element_located(self.DELETE_MODAL))
        confirmation = self.driver.find_element(By.ID, "deleteConfirmation")
        confirmation.send_keys("DELETE")
        self.wait.until(EC.element_to_be_clickable((By.ID, "confirmDeleteBtn"))).click()
        self.wait.until(EC.alert_is_present()).accept()
        self.wait.until(EC.visibility_of_element_located(self.SEARCH))

    @staticmethod
    def _xpath_literal(value: str) -> str:
        if "'" not in value:
            return f"'{value}'"
        return 'concat(' + ', "\'", '.join(f"'{part}'" for part in value.split("'")) + ')'

    def _set_date(self, element, value: str) -> None:
        """Set an HTML date input without Chrome's locale-dependent keystrokes."""
        self.driver.execute_script(
            "arguments[0].value = arguments[1];"
            "arguments[0].dispatchEvent(new Event('input', {bubbles: true}));"
            "arguments[0].dispatchEvent(new Event('change', {bubbles: true}));",
            element,
            value,
        )
