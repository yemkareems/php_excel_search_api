Do composer install to install required libraries

Do symfony serve to start a server locally

All files present in dataSource directory are xlsx

All these files ll be parsed and results ll be shown

For parsing have used box/spout library

All .xlsx files in dataSource directory will be read and parsed to show the search results. 

If in future we need to add more files we just need to place the file in the location and it will be parsed automatically. 

With minimal changes to code can scale the parser to read upto 1 million records.

Basic input validation is in place. 

Service dependency injection is done as per symfony 5.

Response json will have a success or error key based on the condition. If it is success 200 is returned as status and if it fails 500 is sent as status code

Browse

http://127.0.0.1:8000/xlsx?ram=16GB,32GB&location=San%20FranciscoSFO-12&diskType=SATA&storage=1TB-2TB

http://127.0.0.1:8000/xlsx?ram=16GB,32GB

http://127.0.0.1:8000/xlsx?location=San%20FranciscoSFO-12

http://127.0.0.1:8000/xlsx?diskType=SSD

http://127.0.0.1:8000/xlsx?storage=100GB-1TB

Change the search criteria above to search from the list

Run ./bin/phpunit in root directory to run test cases
