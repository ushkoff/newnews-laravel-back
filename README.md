
# NewNews 2022 API v1.

<i><b>Future looks bright.</b></i>

<ul>
    <li>set .htaccess file; allow acces only for frontend app</li>
    <li>php artisan serve</li>
    <li>php artisan migrate --seed</li>
    <li>php artisan passport:install -> Client secret to .env</li>
    <li>php artisan config:cache</li>
    <li>php artisan route:cache</li>
</ul>

# Structure

Main API map located in routes/api_v1.php  

## Articles (News)
<ul>
    <li>ArticlesController/Model/Repository/AfterCreateJob/Requests/Resources/Migration</li>
    <li>LocalNewsController/Repository/Requests</li>
    <li>NewsBlocksController/ArticleRepository/Resources</li>
    <li>NewsRatingController/Models(Likes,Dislikes)/Repositories/AfterDeleteJob/Events/Listeners/Migrations</li>
    <li>Category(Model)/Repository/Migration (controls manually)</li>
</ul>

## Users
<ul>
    <li>UsersController/Model/Repository/Requests/Resources/Migration</li>
    <li>BlockedUserRecordsController/Model/Repository/Request/Resource/Migration</li>
</ul>

## Auth
<ul>
    <li>RegisterController/Request/VerifyEmailNotification</li>
    <li>EmailVerificationController -> special gmail with "Allow less secure apps"</li>
    <li>LoginController/Request -> Laravel Passport OAuth2 Bearer tokens</li>
    <li>PasswordResetRecordController/Model/Repository/Request/Notifications/Migration</li>
</ul>

## Location
<ul>
    <li>CountriesController -> storage\location\countries.json</li>
</ul>

## Google ReCaptcha
<ul>
    <li>VerifyRecaptchaTrait -> .env settings; for all users' actions</li>
</ul>

<br>
All right reserved (c).
