# Latifa Tan | CS370
# 4/6/2023
# use selenium to extract basic information about courses in selected semesters
# all further merging, cleaning and formatting

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import re
import pandas as pd
import string

global hCourse
hCourse = pd.DataFrame({
    'Class Number': [],
    'Class title': [],
    'Class time':[],
    'Class instructor':[]
})

global no_such_person
no_such_person=[]

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
    c=7 # start with
    f = open(text, "r")
    for line in f:
        line= line.strip()
        if line[:4] == "Meet":# when the previous line is "Meets"
            # MEETING TIME
            c =4
        elif c == 4:
            c = 3
            hCourse.at[rowNumber, 'Class time'] = line
        elif c == 3:
            c = 2# 2: the upcoming line is instructor
        elif c == 2:
            # remove puntuation
            hCourse.at[rowNumber, 'Class instructor'] = line
            c = 7 #1: a whole cycle is done
            rowNumber = rowNumber + 1
        elif c==1: # when the previous line is course code like 'CS150'
            # COURSE TITLE
            hCourse.at[rowNumber, 'Class title'] = line
            c = 7 # nothing
        else:
            if len(line) >= 3:
                if line[:2].isupper() and line[1]!=".":
                    hCourse.at[rowNumber, 'Class Number'] = line
                    c = 1
    hCourse.to_csv(name+'.csv')
    return

def rate_prof(full_name,file_prof,current_row,checker,file_class):
    global no_such_person
    no_such_person.append('Staff') # often seen default value
    no_such_person.append('Team') # often seen default value

    if full_name in no_such_person:
        return 'NULL'
    if checker == 1:
        search_last = full_name
        search_initial= 'should not be used'
    else:
        search_initial = full_name[0]
        n=full_name.rindex('.')+1
        search_last = full_name[n+1:]
    if_exist = 0
    for i in range(len(file_prof)):
        if search_last ==file_prof['last_name'][i] and (checker==1 or search_initial == file_prof['first_name'][i][0]):
            file_class.at[current_row,'professor_rate'] = file_prof['professor_rating'][i]
            file_class.at[current_row,'professor_lev_diff'] = file_prof['Level of difficulty'][i]
            if_exist = 1
    if if_exist == 0:
        no_such_person.append(full_name)
    return


def combine_prof(profInfo, classInfo):
    file_class = pd.read_csv(classInfo)
    file_class['professor_rate'] = None
    file_class['professor_lev_diff'] = None
    #f = open(classInfo, encoding="utf-8", errors='replace') # when there is some wired shit in the file
    file_prof =pd.read_csv(profInfo)

    for i in range(len(file_class)):
        name =file_class['Class instructor'][i]
        if '/' in name:
            num_split=name.index('/')
            first_prof = name[:num_split]
            sec_prof = name[num_split+1:]
            rate_prof(first_prof,file_prof,i,1,file_class)
            rate_prof(sec_prof,file_prof,i,1,file_class)
        else:
            rate_prof(name,file_prof,i,0,file_class)
    file_class.to_csv('class_with_rate_2023Fall.csv',index=False)
    return

def time_trans(fileName):
    df=pd.read_csv(fileName)
    for i in range(len(df)):
        end_time =df['end_time'][i] # type---string
        start_time = df['start_time'][i]
        if end_time[-1] == 'p':
            end_hour = end_time[:end_time.index(':')]
            end_min = end_time[end_time.index(':')+1:]+':00'
            if end_hour[:2]!='12':
                end_hour=str(int(end_hour)+12)
            df.at[i,'end_time']= end_hour+end_min
            if start_time[-1] == 'a':
                return
            if len(start_time)==1:
                start_time='0'+start_time+':00:00'
                return


def main():
    campus_option = ['2','3'] # 2 for atlanta; 3 for online
    for c in campus_option: # campus option
        for s in range(1,16): # semester option
            text = search_full(str(s),c)
            print(text)
            print('DONE')
    return

#clean('courseHistory_ Fall2023.txt','courseHistory_ Fall2023')
#df = df.drop_duplicates()

#combine_prof("RateMyProfesor_1250_cleaned.csv", "courseHistory_ Fall2023.csv")
#pre_prof("RateMyProfesor_1250_cleaned.csv")
#time_trans('class_with_rate_2023Fall_less.csv')



