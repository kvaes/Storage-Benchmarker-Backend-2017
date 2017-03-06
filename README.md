General
=======
This repository contains the codebase used for [storage.kvaes.be](https://storage.kvaes.be/). 
This to ensure that you have full transparency what happens with your data when you use the public service. 
On the other hand this repository can be used to host a fully private / isolated service for your own benchmarks.

Installation
============
The backend leverages "Azure Table Storage" for data persistence. The code will look for the following environment variables to establish the connection ; 
* STORAGE_ACCOUNT_NAME = the name for your storage account
* STORAGE_ACCOUNT_KEY = your storage account key

Licensing
=========
* Third party components follow their own license format
* The customizations made are licensed under the [MIT license](https://opensource.org/licenses/MIT)

Support
=======
This repository has been released without any commercial intentions and finds its origin as a kind of hobby project. 
Support can be considered "best effort". If you have any questions, use the [issues tab](https://github.com/kvaes/Storage-Benchmarker-Backend-2017/issues)

Known Issues
============
* [CURL SSL Certificate issue on Azure Webapps](http://techblog.saurabhkumar.com/2016/04/ssl-certificate-problem-on-azure.html)


Third Party Components
=======================
* [CodeIgniter](https://codeigniter.com/)
* [Zingchart](https://www.zingchart.com/)
* [Azure Storage SDK for PHP](https://github.com/Azure/azure-storage-php)