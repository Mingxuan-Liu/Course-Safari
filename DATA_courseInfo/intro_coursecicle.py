from selenium import webdriver
from selenium.common import TimeoutException
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys

import pandas as pd
import string

global hCourse
hCourse = pd.DataFrame({
    'Class Prefix': [],
    'Class Number': [],
    'Class title': [],
    'Class day':[],
    'Class time':[],
    'Class instructor':[],
    'Class location':[]
})

def collect_major(major):
    website = 'https://www.coursicle.com/emory/#search='+major

    driver = webdriver.Chrome()
    driver.set_page_load_timeout(180)  # Replace with the desired timeout in seconds

    driver.get(website)  # Replace with the URL of the webpage you want to load

    wait = WebDriverWait(driver, 20)
    try:
        switch_to_browse = wait.until(EC.element_to_be_clickable((By.XPATH, "//*[@id=\"browseToggleBtn\"]")))
        switch_to_browse.click()

        course_titles = wait.until(EC.presence_of_all_elements_located((By.XPATH, "//div[@class='wrapForToolTip']")))

        # scroll to the bottom of the page
        # Perform Key.DOWN action multiple times to scroll to the bottom of the page
        num_of_scrolls = 2000  # Update with the number of scrolls you need
        for _ in range(num_of_scrolls):
            ActionChains(driver).key_down(Keys.DOWN).perform()

        course_titles = wait.until(EC.presence_of_all_elements_located((By.XPATH, "//div[@class='wrapForToolTip']")))

        for section in course_titles:
            #wait.until(EC.element_to_be_clickable(section))
            # ElementNotInteractableException
            if section.is_displayed():
                section.location_once_scrolled_into_view
                section.click()
                print(section.text)
                print('-------------')
                #driver.implicitly_wait(2)  # make sure the page is loaded
    except TimeoutException as e:
        print(major+"PAGE NOT LOAD timeout exceeded:", e)
    return
#print("dfdsfs\: dfjsfisdf") # add \

def get_prefix():
    full_list = {'BMI', 'TESL', 'IDS', 'W', 'PC', 'NBB', 'ECON', 'JS', 'RUSS', 'CHEM', 'RLPC', 'INTL', 'AMST', 'ANT', 'M', 'RES', 'BAHS', 'GER', 'INFO', 'HPM', 'KRN', 'RP', 'BIOS', 'GH', 'AEPI', 'CB', 'NS', 'JPE', 'NRSG', 'ENGRD', 'HISP', 'RLE', 'BMED', 'BCDB', 'CPLT', 'MED', 'BSHES', 'PRS', 'THEA', 'ARTVIS', 'MATH', 'ICIVS', 'GRK', 'FILM', 'WGS', 'IBS', 'ST', 'PAE', 'BI', 'EHS', 'OCFT', 'PHYS', 'RLNT', 'HNDI', 'RLHT', 'DM', 'SR', 'HIST', 'BL', 'THM', 'MUS', 'DPT', 'GMB', 'EAS', 'RLHB', 'WR', 'PORT', 'BAPS', 'PE', 'ARCH', 'HLTH', 'OISP', 'TBT', 'MDPH', 'SOC', 'WTM', 'MDIV', 'HGC', 'LACS', 'CE', 'CL', 'JPN', 'ARAB', 'ANES', 'MDP', 'ACT', 'OT', 'REES', 'HEBR', 'MI', 'REL', 'BIOL', 'CM', 'PT', 'VMR', 'BUS', 'CHN', 'POLS', 'RLR', 'RLTS', 'LAT', 'MKT', 'MBC', 'RLAR', 'PSYC', 'MSP', 'ENG', 'ENGCW', 'MD', 'ENVS', 'LAW', 'PERS', 'EH', 'ES', 'MESAS', 'AFS', 'MTS', 'PHIL', 'LING', 'ITAL', 'AAS', 'SUST', 'DANC', 'BCS', 'ISOM', 'BIOETH', 'CBSC', 'LA', 'NHS', 'ECS', 'CS', 'VM', 'CHP', 'FREN', 'ARTHIST', 'HC', 'OAM', 'SPAN', 'FIN', 'EPI', 'GHD', 'TATT', 'RE', 'QTM'}
    done_list_1 = {'PSYC', 'RLE', 'MSP', 'SPANOX', 'HLTH', 'BUS', 'ANT', 'ISOM', 'BIOLOX', 'CHN', 'IBS', 'ECS', 'SPAN', 'RES', 'SR', 'HPM', 'TBT', 'RLHB', 'HGC', 'CB', 'EHS', 'OISP', 'LAW', 'HNDI', 'MESAS', 'DANC', 'PC', 'THM', 'ITAL', 'RLAR', 'ENVSOX', 'RP', 'TATT', 'M', 'EAS', 'PHIL', 'BIOL', 'VMR', 'ARTHIST', 'GER', 'MDP', 'RLHT', 'ENVS', 'SUST', 'GMB', 'ECONOX', 'PERS', 'RLR', 'JPE', 'NBB', 'IDS', 'PSYCOX', 'BMI', 'ECON', 'RUSS', 'WTM', 'ANTHOX', 'RLTS', 'LACS'}
    done_list_2 = {'RE', 'MBC', 'PT', 'PERS', 'EPI', 'CHEM', 'OAM', 'CS', 'HIST', 'BMED', 'ARAB', 'MATH', 'KRN', 'FREN', 'HEBR', 'WGS', 'AMST', 'INFO', 'AFS', 'QTM', 'BIOETH', 'GHD', 'ARTVIS', 'VM', 'RLPC', 'REES', 'ACT', 'ARCH', 'PORT', 'MDIV', 'BAHS', 'CL', 'DPT', 'SOC', 'OT', 'GH', 'BI', 'MTS', 'BCS', 'BL', 'OCFT', 'NRSG', 'PE'}

    done_list = done_list_1.union(done_list_2)
    ready_list = full_list.difference(done_list)
    return ready_list

# put txt file into our expect output format
def clean_format(fileName,outputFileName):
    global hCourse
    rowNumber=0
    checker = 10
    f = open(fileName, "r")
    for line in f:
        line= line.strip()
        if line[:4] =='----':
            checker = 8
        elif checker == 8:
            checker = 7
            spliter = line.index(' ')
            hCourse.at[rowNumber, 'Class Number'] = line[spliter+1:]
            prefix_prep = line[:spliter]
            if prefix_prep[-2:] == 'OX':
                prefix_done = prefix_prep[:-2]
            else:
                prefix_done = prefix_prep
            hCourse.at[rowNumber, 'Class Prefix'] = prefix_done
        elif checker == 7:
            # remove pun
            checker = 6
            hCourse.at[rowNumber, 'Class title'] = line.translate(str.maketrans('', '', string.punctuation))
        elif checker == 6:
            checker = 5
            hCourse.at[rowNumber, 'Class day'] = line
        elif checker ==5:
            checker =4
        elif checker == 4:
            line = line.translate(str.maketrans('', '', string.punctuation))
            checker = 3
            if line[-1].isnumeric():
                space_index = line.rindex(' ')
                hCourse.at[rowNumber, 'Class instructor'] = line[:space_index]
            else:
                hCourse.at[rowNumber, 'Class instructor'] = line
        elif checker ==3:
            if line[-1] == '/':
                checker = 2 # next line is also time
                time_1 = line
            else:
                hCourse.at[rowNumber, 'Class time'] = line
                checker = 1 # next line is empty and we dont care
            # time
        elif checker == 2:
            checker = 0
            time_complete = time_1 + line
            hCourse.at[rowNumber, 'Class time'] = time_complete
        elif checker == 1:
            checker =0
        elif checker == 0:
            checker = 10
            hCourse.at[rowNumber, 'Class location'] = line
            rowNumber = rowNumber +1
    hCourse.to_csv(outputFileName+'.csv')
    return
# no symbol OR symbol with \
# no no-meeting time class/ no lab class


def check_duplicate():
    df=pd.read_csv('courseC_short_MAIN.csv')
    prefix = set()
    prefix.update(df['Class Prefix'])
    print(len(prefix))
    print(prefix)
    return

def main():
    prefix_set = get_prefix()
    for department_code in prefix_set:
        collect_major(department_code)
    return

#main()
#get_prefix()
#clean_format('courseC_short_ALL.txt','courseC_short_DONE_ALL')
#check_duplicate()

#get_prefix()

df=pd.read_csv('courseC_short_DONE_ALL.csv')
df = df.drop_duplicates()
df.to_csv('courseC_short_DONE_ALL_nodul.csv')

