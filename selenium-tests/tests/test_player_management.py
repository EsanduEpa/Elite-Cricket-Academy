from datetime import datetime

import pytest
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait

from pages.admin_players_page import AdminPlayersPage, PlayerData
from pages.login_page import LoginPage


def unique_player() -> PlayerData:
    token = datetime.now().strftime("%Y%m%d%H%M%S%f")
    return PlayerData("Selenium", "Test", f"selenium.{token}@example.test", f"selenium_{token}")


@pytest.fixture
def admin_players(driver, base_url, admin_credentials):
    login = LoginPage(driver, base_url)
    login.open()
    login.login(*admin_credentials)
    WebDriverWait(driver, 12).until(EC.url_contains("/admin/dashboard"))
    page = AdminPlayersPage(driver, base_url)
    page.open()
    return page


@pytest.mark.regression
def test_player_007_create_endpoint_rejects_an_underage_player(admin_players):
    player = unique_player()
    alert_text = admin_players.assert_create_validation(player)
    assert "Player must be at least 5 years old" in alert_text


@pytest.mark.regression
def test_player_008_create_search_update_and_delete_isolated_player(admin_players):
    player = unique_player()
    updated_name = "SeleniumUpdated"
    created = False
    try:
        admin_players.create_player(player)
        created = True
        admin_players.search(player.email)
        assert player.full_name in admin_players.driver.find_element(By.ID, "playersTableBody").text

        admin_players.update_player_name(player.email, updated_name)
        admin_players.search(player.email)
        assert updated_name in admin_players.driver.find_element(By.ID, "playersTableBody").text

        admin_players.delete_player(player.email)
        admin_players.search(player.email)
        assert player.email not in admin_players.driver.find_element(By.ID, "playersTableBody").text
        created = False
    finally:
        if created:
            # Best-effort cleanup only targets the uniquely generated Selenium record.
            try:
                admin_players.delete_player(player.email)
            except Exception:
                pass
