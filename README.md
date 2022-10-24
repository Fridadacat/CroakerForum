# Croaker
 Croaker Webforum

Nach dem sie das Zipfile im htdocs folder ihrer lokalen xamppinstallation entpackt haben, oder sie das GitRepository in den folder kopiert haben, starten sie den Apachee und Mysql Dienst und öffnen sie die xampp konsole.

geben sie folgende befehle ein: mysql -u root
                                create database mydb;
                                ctrl+c
                                mysql -u root mydb < mysqlscript.txt
                                mysql -u root
                                CREATE USER 'croaker'@'localhost' IDENTIFIED BY 'croaker123';
                                GRANT CREATE, INSERT, UPDATE, DELETE, SELECT ON mydb.* TO 'croaker'@'host';
                                FLUSH PRIVILEGES;
                                ctrl + c

Starten sie nun den mysql und den apacheedienst neu und öffnen sie ihren browser. Nun sollten sie auf der croakerwebseite sein wenn sie sich zu "localhost" verbinden.

                                

