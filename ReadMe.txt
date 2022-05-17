DBhandler

This is a database handler that works on MySQL servers on e.g. web hotels. 
- Written in PHP.
- Suplies classes for external inputs. 
- 

Use
1. Require /DBhandler/Main.php and the incomingDataClasses
2. Create an instance of DBhandler.
3. Create an instance of e.g. StorePost-class, which will then tell you what is required.
4. Eventual required POSTdata is an array with column-name => value. You should take the column-names from your database, and make sure the values agree with the entity formats. 
5. Call the correct method in DBhandler and submit the incomingData-object.
