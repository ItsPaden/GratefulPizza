from selenium import webdriver
from selenium.webdriver.chrome.options import Options
import time

#targetEmail = "rriedhammer@sheltersofsaratoga.org"
targetEmail = "togagratefulpizza@gmail.com"
targetURL = "https://www.paypal.com/us/gifts/brands/domino-s-pizza"
password = "PadenNick123!"

options = webdriver.ChromeOptions()
options.add_argument("user-data-dir=C:\\Users\\Paden\\AppData\\Local\\Google\\Chrome\\User Data\\Default")
driver = webdriver.Chrome(executable_path="C:\\Users\\Paden\\Downloads\\chromedriver_win32\\chromedriver.exe", chrome_options=options)

driver.get(targetURL);

# Wait for site to load
time.sleep(5)

otherBox = driver.find_element_by_xpath("//input[@name='amount' and @value='other']")
otherBox.click()

time.sleep(2)

paymentAmount = userID = driver.find_element_by_xpath(".//*[@id='input']")
paymentAmount.send_keys("5")

time.sleep(2)

applyButton = userID = driver.find_element_by_xpath(".//*[@class='css-14r95tt vx_btn']")
applyButton.click()

time.sleep(1)

inputEmail = driver.find_element_by_name("form-control_complex_email")
inputEmail.send_keys(targetEmail)

inputName = driver.find_element_by_name("form-control_complex_name")
inputName.send_keys("GratefulPizza")

addToCart = driver.find_element_by_name("Next")
addToCart.click()

time.sleep(3)

checkout = driver.find_element_by_name("Checkout")
checkout.click()

time.sleep(3)

buyNow = userID = driver.find_element_by_xpath(".//*[@class='css-1ejcnzd vx_btn']")
buyNow.click()

#Log In

time.sleep(8)

logIn = driver.find_element_by_name("login_password")
logIn.send_keys(password)

logInButton = driver.find_element_by_name("btnLogin")
logInButton.click()

#Enter gift info




