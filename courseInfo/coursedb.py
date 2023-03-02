import urllib.request
from bs4 import BeautifulSoup
import pandas as pd

#from openpyxl import Workbook
# 370 web scraping from:
# https://m.opus.emory.edu/app/catalog/listsubjects/EMORY/5239

df = pd.DataFrame({
    'Class Number': [], # class number: 0000-9999 could be used to linked to web page
    'Department Number':[], # from the top main title bar
    'Course Name': [], # from the upper left bar small title bar
    'Units': [],
    'Grading': [],
    'Add Consent': [],
    'Enrollment Requirements': [],
    'Instructor(s)': [],
    'Meets': [],
    'Campus': [],
    'Class Capacity': [],
    'Description': []
})

urlNullList = [] # the list of url that we want to try
rowNum = 0
dfHead = df.head() # just save time checking

def lookup(tryNum):# set print for each part
    # things we want globally
    global urlNullList
    global rowNum
    global dfHead
    c = None
    # prep to open html: 5239 - 2023fall
    root_url_1='https://m.opus.emory.edu/app/catalog/classsection/EMORY/5239/'
    url=root_url_1+ tryNum #full url
    response=urllib.request.urlopen(url,timeout=100)
    html=response.read()
    soup=BeautifulSoup(html,'lxml')

    # course_code
    depN_potential = soup.find_all('h1')
    depN_potential=str(depN_potential)
    null_depN ="Class Details" # check if we have anything this page
    if null_depN in depN_potential:
        urlNullList.append(tryNum)
        return
    else:
        df.at[rowNum, 'Department Number'] = depN_potential[38:-6]
        name_potential = soup.find_all("div", class_="primary-head")
        context_potential = soup.find_all("div",class_ = "section-content clearfix")
        for name in name_potential:
            c = name.get_text()
            c = c.strip()
            df.at[rowNum, 'Course Name'] = c
            print(c)
            break
        if c is None:
            return
        print(rowNum)
        print(tryNum)
        for example in context_potential:
            a = example.get_text()
            b = a.split("\n")
            colName = b[2]
            targetValue = b[5]
            if colName in dfHead:
                df.at[rowNum, colName] = targetValue
        rowNum =rowNum +1
        return


def main():
    #for i in range(1,10000):
        #stri = str(i)
        #tryNum = "0"*(4-len(stri))
        #n = tryNum+ stri
        #lookup(n)

    #testing
    for i in range(1000,1400):
        lookup(str(i))

main()
#lookup("3601")
df.to_csv('test_course.csv')
print("we miss", len(urlNullList))
print(urlNullList)