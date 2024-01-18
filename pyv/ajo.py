import mysql.connector

dbhost = "localhost"
dbuser = "root"
dbpass = "JSRy5hebnghwGBVW3"
dbname = "tamkonto"

#mydb = mysql.connector.connect(host=dbhost,user=dbuser,password=dbpass,database=dbname)
#mycursor = mydb.cursor()

#sql = "INSERT INTO trans (uid, account, additionalInformation,bookingDate,creditorName,debtorName,entryReference,remittanceInformationUnstructured,transactionId,valueDate,amount,currency,credit,debit,merchantCategoryCode,bban,status,fetchdate,boodate,boostamp, boomonth, booyear) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
#val = (brand, btype, part_en, part_nl, path_en, path_nl, part_woo_nummer, partsku)

#mycursor.execute(sql, val)
#mydb.commit()


mydb = mysql.connector.connect(host=dbhost,user=dbuser,password=dbpass,database=dbname)
mycursor = mydb.cursor()

sql = "select tamdata.value from tamdata where tamdata.key='TOKEN'"

mycursor.execute(sql)
result = mycursor.fetchall()

for x in result:
  print(x)
