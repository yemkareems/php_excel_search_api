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

https://yemkareems.000webhostapp.com/xlsx?ram[]=16GB&ram[]=32GB&location=San%20FranciscoSFO-12&diskType=SATA&storageFrom=1TB&storageTo=2TB

Sample Response

{"success":"ok","searchCount":14,"searchResult":[["Dell R210-IIIntel Xeon E3-1270v2","16GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$121.99"],["DL20G9Intel Xeon E3-1270v5","16GBDDR4","2x1TBSATA2","San FranciscoSFO-12","$135.99"],["DL20G9Intel Xeon E3-1270v5","16GBDDR4","2x1TBSATA2","San FranciscoSFO-12","$135.99"],["HP DL380pG82x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$225.99"],["IBM X3650M42x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$220.99"],["Dell R210-IIIntel Xeon E3-1270v2","16GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$151.99"],["HP DL380pG82x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$255.99"],["IBM X3650M42x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$250.99"],["Dell R210-IIIntel Xeon E3-1270v2","16GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$127.99"],["IBM X3650M42x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$226.99"],["HP DL380pG82x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$231.99"],["Dell R210-IIIntel Xeon E3-1270v2","16GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$288.79"],["HP DL380pG82x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$392.79"],["IBM X3650M42x Intel Xeon E5-2620","32GBDDR3","2x1TBSATA2","San FranciscoSFO-12","$387.79"]]}

https://yemkareems.000webhostapp.com/xlsx?diskType=SAS&location=FrankfurtFRA-10

{"success":"ok","searchCount":3,"searchResult":[["HP DL380eG82x Intel Xeon E5-2420","32GBDDR3","8x300GBSAS","FrankfurtFRA-10","\u20ac176.99"],["HP DL180G62x Intel Xeon E5620","32GBDDR3","8x300GBSAS","FrankfurtFRA-10","\u20ac166.99"],["HP DL180G62x Intel Xeon E5620","32GBDDR3","8x300GBSAS","FrankfurtFRA-10","\u20ac280.99"]]}

https://yemkareems.000webhostapp.com/xlsx?storageFrom=100TB

{"error":"No data found for the search"}

https://yemkareems.000webhostapp.com/xlsx?storageFrom=

{"error":{"[storageFrom]":["The value you selected is not a valid choice."]}}

Possible values for search query params

`
'storageFrom' => ['0GB', '250GB', '500GB', '1TB', '2TB', '3TB', '4TB', '8TB', '12TB', '24TB', '48TB', '72TB', '100TB'],
'storageTo' => ['0GB', '250GB', '500GB', '1TB', '2TB', '3TB', '4TB', '8TB', '12TB', '24TB', '48TB', '72TB', '100TB'],
'ram' => ['2GB', '4GB', '8GB', '12GB', '16GB', '24GB', '32GB', '48GB', '64GB', '96GB', '128GB'],
//for ram mulitple is possible like ram[]=4GB&ram[]=8GB
'diskType' => ['SAS', 'SATA', 'SSD'],
'location' => ['AmsterdamAMS-01', 'DallasDAL-10', 'FrankfurtFRA-10', 'Hong KongHKG-10', 'San FranciscoSFO-12', 'SingaporeSIN-11', 'Washington D.C.WDC-01'],
));
`

https://yemkareems.000webhostapp.com/xlsx?ram[]=96GB

{"success":"ok","searchCount":1,"searchResult":[["Dell R6202x Intel Xeon E5-2650","96GBDDR3","8x120GBSSD","AmsterdamAMS-01","\u20ac191.99"]]}

https://yemkareems.000webhostapp.com/xlsx?ram=4GB

{"error":{"[ram]":["This value should be of type array."]}}

https://yemkareems.000webhostapp.com/xlsx?ram[]=4GB&ram[]=9GB

{"error":{"[ram]":["One or more of the given values is invalid."]}}

https://yemkareems.000webhostapp.com/xlsx?ram[]=4GB

{"success":"ok","searchCount":21,"searchResult":[["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","\u20ac39.99"],["Dell R210-IIIntel G530","4GBDDR3","2x500GBSATA2","AmsterdamAMS-01","\u20ac35.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","\u20ac163.99"],["Dell R210-IIIntel G530","4GBDDR3","2x500GBSATA2","AmsterdamAMS-01","\u20ac60.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","\u20ac80.99"],["Dell R210-IIIntel G530","4GBDDR3","2x500GBSATA2","AmsterdamAMS-01","\u20ac40.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","\u20ac60.99"],["Dell R210-IIIntel G530","4GBDDR3","2x500GBSATA2","AmsterdamAMS-01","\u20ac174.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","\u20ac194.99"],["HP DL120G7Intel G850","4GBDDR3","4x500GBSATA2","AmsterdamAMS-01","\u20ac1775.99"],["HP DL120G6Intel G6950","4GBDDR3","4x500GBSATA2","Washington D.C.WDC-01","$49.99"],["HP DL120G6Intel G6950","4GBDDR3","4x500GBSATA2","Washington D.C.WDC-01","$43.99"],["Dell R5102x Intel Xeon E5504","4GBDDR3","4x1TBSATA2","Washington D.C.WDC-01","$104.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","SingaporeSIN-11","S$119.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","Washington D.C.WDC-01","$97.99"],["HP DL120G6Intel G6950","4GBDDR3","4x500GBSATA2","Washington D.C.WDC-01","$79.99"],["HP DL120G6Intel G6950","4GBDDR3","4x500GBSATA2","Washington D.C.WDC-01","$55.99"],["Dell R5102x Intel Xeon E5504","4GBDDR3","4x1TBSATA2","Washington D.C.WDC-01","$110.99"],["HP DL120G6Intel Xeon X3440","4GBDDR3","2x500GBSATA2","Washington D.C.WDC-01","$236.79"],["HP DL120G6Intel G6950","4GBDDR3","4x500GBSATA2","Washington D.C.WDC-01","$216.79"],["Dell R5102x Intel Xeon E5504","4GBDDR3","4x1TBSATA2","Washington D.C.WDC-01","$271.79"]]}

https://yemkareems.000webhostapp.com/xlsx?location=AmsterdamAMS-01

https://yemkareems.000webhostapp.com/xlsx?diskType=SSD

https://yemkareems.000webhostapp.com/xlsx?storageFrom=250GB&storageTo=1TB

https://yemkareems.000webhostapp.com/xlsx?storageTo=1TB

If storageTo alone is given, default storageFrom is 0GB

https://yemkareems.000webhostapp.com/xlsx?storageFrom=500GB

If strogeFrom alone is given, default storageTo is 100TB

Change the search criteria above to search from the list

Run ./bin/phpunit in root directory to run test cases
