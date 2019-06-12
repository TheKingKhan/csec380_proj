import time
import os
from selenium import webdriver

driver = webdriver.Chrome('Downloads/chromedriver')  # Optional argument, if not specified will search path.
driver.get('http://localhost');


#Auth test
user_login = driver.find_element_by_name('user')
user_login.send_keys('somename@somesite.com')
password_login = driver.find_element_by_name('pwd')
password_login.send_keys('password')
password_login.submit()
time.sleep(5)
#Post by local file
driver.find_element_by_name("file_to_upload").send_keys(os.getcwd()+"/small.mp4")
driver.find_element_by_name("video_name").send_keys("Test Local File")
time.sleep(5)
driver.find_element_by_name("video_name").submit()
time.sleep(5)

#delete the posts
driver.find_element_by_id("delButton").submit()
time.sleep(5)

#Post by link
driver.find_element_by_name("video_link").send_keys("https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4")
driver.find_element_by_name("video_name").send_keys("Test File Link")
driver.find_element_by_name("video_name").submit()
#Go to presentation page
driver.find_element_by_id("postContent").click()
time.sleep(5)



driver.get('http://localhost/home.php');
driver.find_element_by_id("getSettings").click()
driver.find_element_by_name("displayName").send_keys("<script> window.location.replace('home.php?id=1') </script>")
driver.find_element_by_id("submitButton").submit()
