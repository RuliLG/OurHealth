# OurHealth: Sharing patients medical records across different hospitals in the Canary Islands

## Context
The two major islands from the Canary Islands, Tenerife and Gran Canaria, have the following hospitals:

Island | Hospital
----|-----
Gran Canaria|Hospital Dr. Negrín
Gran Canaria|Hospital Insular
Tenerife|Hospital Universitario de Canarias
Tenerife|Hospital Universitario Nuestra Señora de la Candelaria

The other six islands (*La Palma*, *La Gomera*, *El Hierro*, *Fuerteventura*, *Lanzarote* and *La Graciosa*), although they may have a major hospital, they don't have many services like hematology or cardiology. This is why, for example, when someone from Fuerteventura suffers from a heart attack, they must be transported to Gran Canaria using a helicopter.

Every hospital from the Canary Islands runs an application named "Drago". After talking with a few workers from *Hospital Dr. Negrín* we noticed several issues with the way everything is working right now.

Although this problem may be present in other provinces and countries (for example, the French region *Bretagne* also has this problem), we are focusing on the Canary Islands' problems for the scope of this solution.

## El Drago
### Outdated system
First of all, the application was created almost 20 years ago and it has been updated a few times just to introduce some workflow improvements. This is a current screenshot of the application:

![](https://ourhealth.raullgdev.es/assets/drago.jpg?ver=1)

Searching through the local newspapers, we can read a few articles about El Drago being unusable:

- https://www.canarias7.es/hemeroteca/247_pacientes_y_un_fallo__informatico_en_24_horas_-PECSN445794
- https://www.canarias7.es/canarias/gran-canaria/las-palmas-de-gran-canaria/pagara-300000-euros-20201219005648-nt.html

Also, the current system makes it too difficult to make trivial changes, [like removing transsexuality from the disease list](https://www.laprovincia.es/sociedad/2020/02/05/vox-culpa-sanidad-sistema-informatico-8319614.html).

### Bad UX. Slow
Back in 2017, Antonia Rodríguez - SEMERGEN president - gave the following statement to the media, arguing the Canary Islands need **an urgent modification of Drago** (which still has not happened):

> We only have 6 minutes per patient. It's simply not possible to fill 50 fields, so many times we just reflect some data in the patient's medical records and we don't follow the protocol.
> 
> [Learn more in Canarias7](https://www.canarias7.es/sociedad/la-atencion-primaria-primera-linea-de-sospecha-de-la-violencia-de-genero-GJ975989)

This may not seem an important problem, as the data is still being reflected. The real problem is that this data is not structured, it's just filled inside one field, making it impossible to perform any data analysis techniques or queries against the database. Also, as every doctor fill it their own way, it's not fast either to search through a patient's medical records.

### Sharing medical records
As patients are assigned to a certain hospital, when they need to go to another hospital they won't be able to access the patient's medical records, adding new burocratic steps to a process that need to be as fast as possible. For example, if someone suffers a heart attack in Fuerteventura they need to be taken to Hospital Dr. Negrín, but they'd need the patient data to be fully certain about how they should intervene.

This problem may be easily solved if the patient is accompanied by another family member who can fill the medical records quickly, but this is not always possible and the hospital must contact the other island to request the medical records via email.

We don't even have to travel to another island: *Hospital Insular* does not have a digestive service, so patients are treated in *Hospital Dr. Negrín*. When they come back to their doctor, they cannot see any report as it's stored in another system.

Although they've tried to [update the application to share data between hospitals](https://www.canarias7.es/hemeroteca/se_activa_la_historia_clinica_compartida_entre_las_unidades_de_medicina_intensiva_del_servicio_canario_de_salud-MGCSN205832), this is only possible in the ICU unit.

## Approaching the problem

### How does the current program work?
El Drago can be accessed via VPN, having each doctor their own user and password.

Each hospital has their own instance of the application, even in the same island: *Hospital Dr. Negrín* calls it "Drago" and *Hospital Insular* calls it "Tabaiba", although they run the same software. This makes it almost impossible to share any data between hospitals.

Despite of this problem, each patient is uniquely identified by their Health ID, so it should be relatively easy to consolidate every database to create records for each patient.

### Setting up the database
We've setup a relational database, following this ER diagram:
![](https://ourhealth.raullgdev.es/assets/er_diagram.png)

### Backend
A powerful server might be capable of holding and processing all this data and requests from the Canary Islands, but we thought it might be better to create a scalable infrastructure that could handle many more hospitals from different places.

We think it would be best to use AWS powered by Amazon Aurora for the database, S3 for file storage as a CDN and multiple EC2 servers with load balancing.

For the scope of this project, we've just built it in our own VPS server, using a standard MySQL database and standard file storage.

### Frontend: Cross-platform
To improve security, we think it's better to develop a desktop application instead of just using a website, so that the desktop application is only installed in the hospitals.

We would choose to use https://electronjs.org/, which is a Chromium-powered engine that will let us create a hybrid desktop application using web technologies. This way, we could develop the app using, for example, Angular or Vue, and reuse most of it if we want to create a website at some point.

### Offline?
Of course, if we want to share data between hospitals, then we need internet connection. Currently, **Drago does not work if it doesn't have internet connection**.

By building an Electron application, we should be able to leverage local caching each time we download something from the API, so that the app could keep working (at least with read permissions) without internet.

### Security
When we're talking about security, hospital data may be the most important one. AWS has a lot of certifications to show that they can handle security.

Even if we were *scared* to use an AWS server stored in other country, we can still decide to create our own data center in Spain.

Currently, at least in Drago, every doctor can access data from every patient. This requests are stored and can be reviewed, so they can get in real trouble if they're found to be reviewing data from other patients.

With our approach, it would be possible to give doctors access to **only their patients**, so when a new appointment is scheduled for them, they will be able to access to this new patient.

Also, we've developed a *role* attribute, which would allow head of departments to access more sensible data or perform certain actions on some patients. There's also another *superadmin* role, which can do basically everything.

### Try out the API

Please note that every API route is protected using a token, which is regenerated each time the user logs in. You can log in using your user and password at */login*.

#### Test credentials
You can also use this credentials to have superadmin access, which will allow you to create your own user:

- Email: hi@raullg.com
- Password: MyPassw0rd

After logging in, you will get a token you can use to authenticate further requests.

#### Online
API is currently hosted on [https://ourhealth.raullgdev.es/api/v1](https://ourhealth.raullgdev.es/api/v1). You can [check out the documentation](https://raullg.stoplight.io/docs/OurHealth-Docs/OurHealth.v1.yaml) for more information.

#### Locally
The API can be installed in your computer by following this steps:

First of all, ensure your computer has PHP 7.3, Composer and MySQL installed.

Then, clone the repository in your computer.

```bash
$ git clone https://github.com/RuliLG/OurHealth.git
```

Navigate to the directory and install the dependencies.

```bash
$ cd ourhealth
$ composer install
```

After the dependencies are installed, you should create an .env file and generate a new key:

```bash
$ cp .env.example .env
$ php artisan key:generate
```

Then, modify the .env file with your database settings:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT={YOUR_DB_PORT}
DB_DATABASE={YOUR_DB_NAME}
DB_USERNAME={YOUR_DB_USERNAME}
DB_PASSWORD={YOUR_DB_PASSWORD}
```

And finally, run the migrations to setup the database locally:

```bash
$ php artisan migrate
```

Also, we've made it easier by automatically filling the countries and regions tables. You can populate the database with the following command:

```bash
$ php artisan populate
```

Then, you should be able to perform requests at http://localhost:8000/api/v1

Once again, you can check out the API documentation [here](https://ourhealth.raullgdev.es/api/v1).

### Insomnia
We've also prepared an [Insomnia Core](https://insomnia.rest/download/) Workspace with every endpoint already prepared. You just have to import it into Insomnia and create an environment with the following variables:

```json
{
  "host": "https://ourhealth.raullgdev.es/api/v1",
  "token": "LOGIN_TOKEN"
}
```

You can download it by [clicking here](https://ourhealth.raullgdev.es/assets/OurHealth-20200104.json)

