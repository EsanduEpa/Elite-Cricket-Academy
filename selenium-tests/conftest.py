"""Shared Selenium and environment configuration for Elite UI tests."""

from __future__ import annotations

import os
import tempfile
from pathlib import Path

import pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service


def pytest_addoption(parser: pytest.Parser) -> None:
    group = parser.getgroup("elite")
    group.addoption("--base-url", default=os.getenv("ELITE_BASE_URL", "http://localhost/Elite/public"))
    group.addoption("--headed", action="store_true", default=False, help="Show Chrome while tests run.")


@pytest.fixture(scope="session")
def base_url(pytestconfig: pytest.Config) -> str:
    return pytestconfig.getoption("--base-url").rstrip("/")


@pytest.fixture(scope="session")
def admin_credentials() -> tuple[str, str]:
    """Credentials are configurable so no production secret is committed."""
    return (
        os.getenv("ELITE_ADMIN_USERNAME", "admin2"),
        os.getenv("ELITE_ADMIN_PASSWORD", "password"),
    )


@pytest.fixture
def driver(pytestconfig: pytest.Config):
    options = Options()
    if not pytestconfig.getoption("--headed"):
        options.add_argument("--headless=new")
    options.add_argument("--window-size=1440,1100")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--no-sandbox")
    options.add_argument(f"--user-data-dir={tempfile.mkdtemp(prefix='elite-selenium-')}")
    options.add_argument("--remote-debugging-port=0")
    options.add_experimental_option("excludeSwitches", ["enable-logging"])

    default_driver = next((Path(__file__).parent / ".selenium").glob("chromedriver/win64/*/chromedriver.exe"), None)
    driver_path = os.getenv("ELITE_CHROMEDRIVER", str(default_driver) if default_driver else "")
    service = Service(executable_path=driver_path) if driver_path else Service()
    browser = webdriver.Chrome(service=service, options=options)
    browser.implicitly_wait(0)
    yield browser
    browser.quit()
