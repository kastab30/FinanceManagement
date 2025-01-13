# FinanceManagement
This web-based portal helps you manage your finances efficiently. Imagine lending money to many people and still relying on pen and paper for records. Why not have your own personal website for easy management? Here it is! Enjoy full data privacy, complete control, and say goodbye to expensive premium plans. Simplify your financial tracking today!
## Installation

Download ZIP Or Clone it.

```bash
  mkdir FinanceManagement
```

```bash
  cd FinanceManagement
```

```bash
  git clone https://github.com/kastab30/FinanceManagement.git
```

Download XAMPP For using MYSQL DATABASE

Link: https://www.apachefriends.org/download.html

Open and Click on Start ```Apache & MYSQL```

![App Screenshot](https://github.com/kastab30/FinanceManagement/blob/8ff6bcc829f78ccb9cf1b8cf562a62f2643d9502/static/image/XAMP1.png)

Click on Shell and Execute the following commands : 

```bash
  mysql -u root
```

![App Screenshot](https://github.com/kastab30/FinanceManagement/blob/c3dc2612ee2dfcca45cfd8b0142aa3afd47dbc20/static/image/XAMP2.png)



Create New Database : ```pay```
```bash
  create database pay;
```

Visit : http://localhost:/phpmyadmin/

Select the Database and click Import. Then scroll down and ```Go``` 

![App Screenshot](https://github.com/kastab30/FinanceManagement/blob/1c994a9d2654adee69b151b9fb03564ce4f7180b/static/image/phpmyadmin.png)

You can create your own database with your own db name. In that case you need to update the details in ```config.php``` and 
```admin/delete_user.php```.

You can access it by visiting ```localhost``` or ```127.0.0.1```
    
## Screenshots

![App Screenshot](https://github.com/kastab30/FinanceManagement/blob/163c8d8ab4b297948ca685a5951d666ba129dc1b/static/image/SignIn.png)
