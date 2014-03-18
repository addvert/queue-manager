<<<<<<< HEAD
Queue Manager
=========

*Early development state: only supports Amazon Sqs and not much testing done*

Simple Queue Job Manager thinked for CodeIgniter

You need to run composer to fetch the Amazon AWS Sdk

It works as standalone, but should work with CodeIgniter as a library. 
However, you need to figure out how to include Amazon AWS Sdk without Composer

***
Usage
--------------

Edit the config.php with your Aws credentials


Enqueue new job
```php
$job = new Queue();
$job->create('controller', 'method', array('param1'), 'Test Job');
```
Worker (possibly to be used with Supervisord)
```php
$job = new Queue();
$job->worker();
```