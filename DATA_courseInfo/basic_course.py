from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import re
import pandas as pd

global hCourse
hCourse = pd.DataFrame({
    'Class Number': [],
    'Class title': []
})

def search_full(sNum,cNum):
    driver = webdriver.Chrome()
    driver.get("https://atlas.emory.edu/")  # Replace with the URL of the webpage you want to load

    # Wait for the "any campus" drop down menu to be clickable
    wait = WebDriverWait(driver, 20)
    # Select Campus
    any_campus_menu = wait.until(EC.element_to_be_clickable((By.XPATH, "//*[@id=\"crit-content-1464624409188\"]/div[4]")))
    any_campus_menu.click()
    c_string = "//*[@id=\"crit-camp\"]/option["+cNum+"]"
    campus_option = driver.find_element(By.XPATH, c_string)
    campus_option.click()
    # Select semester
    semester_menu = wait.until(EC.element_to_be_clickable((By.XPATH, "//*[@id=\"crit-content-1464624409188\"]/div[2]")))
    semester_menu.click()
    s_string = "//*[@id=\"crit-srcdb\"]/option["+sNum+"]"
    semester_option = driver.find_element(By.XPATH, s_string)
    semester_option.click()
    # click search
    search_click = driver.find_element(By.XPATH, "//*[@id=\"search-button\"]")
    search_click.click()
    # Wait for the course titles to load
    course_titles = wait.until(EC.presence_of_all_elements_located((By.XPATH, "//div[@class='result result--group-start']")))
    # basic class info
    c_name= driver.find_element(By.XPATH, "//body[@class=\"user-anon\"]").text

    pattern_s = r"Found \d+ courses"
    pattern_e = r"Search Criteria"
    match_s = re.search(pattern_s, c_name)
    match_e = re.search(pattern_e, c_name)
    start_index = match_s.end() + 1
    end_index = match_e.start() + 1
    c_name = c_name[start_index:end_index]
    driver.quit()
    return c_name # clean text with packed course info

def clean(text,name):
    global hCourse
    rowNumber =0
    c=5 # start with
    f = open(text, "r")
    for line in f:
        line= line.strip()
        if c == 0:# when the previous line is "Meets"
            # MEETING TIME
            c=2
            continue
        elif c==1: # when the previous line is course code like 'CS150'
            # COURSE TITLE
            hCourse.at[rowNumber, 'Class title'] = line
            rowNumber = rowNumber + 1
            c=2
        elif line[0:3] == "Mee":
            c=0
        else:
            if len(line) >= 3:
                if line[:2].isupper() and (line[-1].isupper() or line[-1].isnumeric()):
                    c=1
                    hCourse.at[rowNumber, 'Class Number'] = line
    hCourse.to_csv(name+'.csv')
    return

def main():
    campus_option = ['2','3'] # 2 for atlanta; 3 for online
    for c in campus_option: # campus option
        for s in range(1,16): # semester option
            text = search_full(str(s),c)
            print(text)
            print('DONE')
    return

#clean('courseHistory_All.txt','courseHistory_All')
#df = df.drop_duplicates()
