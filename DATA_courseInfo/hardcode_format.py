# 4/11/2023
# CS370
# Latifa Tan
# transform .csv file into sql format for insert

import pandas as pd
df=pd.read_csv('class_with_rate_2023Fall_less_split.csv') # FILENAME for the hardcoded file
sql_text =[]
for index, row in df.iterrows():
    if len(row['start_time'])<8:
        start_time = '0'+ row['start_time']
    else:
        start_time = row['start_time']
    if len(row['end_time'])<8:
        end_time = '0'+ row['end_time']
    else:
        end_time = row['end_time']

    txt = "(\'"+row['course_code_char']+"\',"+\
          str(row['course_code_num'])+","+\
          "\'"+row['course_name']+"\',"+ \
          "\'"+row['days']+"\',"+ \
          "\'" + start_time + "\'," + \
          "\'" + end_time + "\'," + \
          "\'" + row['professor'] + "\'," + \
          str(row['professor_rate'])+","+\
          str(row['professor_lev_diff'])+"),"
    sql_text.append(txt)

df['sql_text'] = sql_text
df.to_csv('class_with_rate_2023Fall_less_split.csv')