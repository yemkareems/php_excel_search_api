Do composer install to install required libraries

Do symfony serve to start a server locally

All files present in dataSource directory are xlsx

All these files ll be parsed and results ll be shown

For parsing have used box/spout library

It can parse both xlsx and csv files also

All .xlsx files in dataSource directory will be read and parsed to show the search results. 

If in future we need to add more files we just need to place the file in the location and it will be parsed automatically. 

With minimal changes to code can scale the parser to read upto 1 million records.

Basic input validation is in place. 

Service dependency injection is done as per symfony 5.

Response json will have a success or error key based on the condition. If it is success 200 is returned as status and if it fails 500 is sent as status code

Browse

http://127.0.0.1:8000/xlsx?ram[]=16GB&ram[]=32GB&location=San%20FranciscoSFO-12&diskType=SATA&storageFrom=1TB&storageTo=2TB

Sample Response

{"success":"ok","data":[["Dell R210-IIIntel Xeon E3-1270v2","16GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$121.99"],["DL20G9Intel Xeon E3-1270v5","16GBDDR4","2x1TBSATA2","San FranciscoSFO-12","$135.99"],["DL20G9Intel Xeon E3-1270v5","16GBDDR4","2x1TBSATA2","San FranciscoSFO-12","$135.99"],["HP DL380pG82x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$225.99"],["IBM X3650M42x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$220.99"],["Dell R210-IIIntel Xeon E3-1270v2","16GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$151.99"],["HP DL380pG82x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$255.99"],["IBM X3650M42x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$250.99"],["Dell R210-IIIntel Xeon E3-1270v2","16GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$127.99"],["IBM X3650M42x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$226.99"],["HP DL380pG82x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$231.99"],["Dell R210-IIIntel Xeon E3-1270v2","16GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$288.79"],["HP DL380pG82x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$392.79"],["IBM X3650M42x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$387.79"]]}

http://127.0.0.1:8000/xlsx?storageFrom=100TB

{"error":"No data found for the search"}

http://127.0.0.1:8000/xlsx?storageFrom=

{"error":{"[storageFrom]":["The value you selected is not a valid choice."]}}

http://127.0.0.1:8000/xlsx?ram[]=16GB&ram[]=32GB

http://127.0.0.1:8000/xlsx?location=AmsterdamAMS-01

http://127.0.0.1:8000/xlsx?diskType=SSD

http://127.0.0.1:8000/xlsx?storageFrom=250GB&storageTo=1TB

http://127.0.0.1:8000/xlsx?storageTo=1TB

http://127.0.0.1:8000/xlsx?storageFrom=500GB

Change the search criteria above to search from the list

Run ./bin/phpunit in root directory to run test cases
