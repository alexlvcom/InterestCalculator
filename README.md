# Interest Calculator
---

#### A completion of a sample task - to create service which connects to RabbitMQ server, listens on the queue for messages. For each message it calculates "interest" and total sum by given formula. After that it broadcasts the message with the result to another queue.

#### Interest formula
>Interest is calculated based on sum and days fields  
>Interest is calculated per day as a percentage from the original amount  
>If day is...  
>    divisible by three, the interest is: 1%  
>    divisible by five, the interest is: 2%  
>    divisible by both three and five, the interest is: 3%  
>    not divisible by either three or five, interest is: 4%  
>Each day interest amount is rounded to two digits  
>Final interest is a sum of all days interests  
>Total sum is the sum of original amount and total interest
  
#### Example

| Day        | Interest %           | Interest ammount  |
| ------------- |:-------------:| -----:|
| 1 | 4%        | 4.92 |
| 2 | 4%        |   4.92 |
| 3 | 1% (day is divisible by three)      |  1.23 |
| 4 | 4% | 4.92 |
| 5 | 2% (day is divisible by five) | 2.46|
|   | Total interest: | 18.45 |
|   | Total sum: | 141.45 |



#### Message Format
Incoming messages look like `{ "sum": 123, "days": 5 }`   
Outgoing messages look like `{ "sum": 123, "days": 5, "interest": 18.45, "totalSum": 141.45, "token": "myIdentifier" }`
 
 #### Dependencies:
 - PHP >= 5.5
 - [php-amqplib](https://github.com/videlalvaro/php-amqplib)
 
#### Installation
 - `composer install`

#### Usage
  - `php src/interest.php`