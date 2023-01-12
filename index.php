<?php
// when installed via composer
//require_once 'vendor\autoload.php';
require_once 'Database.php';

Database::use("tamagotchi_bcl");
// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

Database::disableForeignKeyChecks();
foreach([ "account", "tamagotchis", "actions"] as $table)
{
    Database::truncate($table);
}
Database::enableForeignKeyChecks();

//$seedConfig = [
//    "accounts" => 1,
//    "tamagotchis" => 1,
//    "actions" => 1
//];
//
//$clientsToInsert = [];
//for($i = 0; $i < $seedConfig["accounts"]; $i++) {
//    $clientsToInsert[] = [
//        $faker->name(),
//    ];
//}
//
//$sellersToInsert = [];
//for($i = 0; $i < $seedConfig["tamagotchis"]; $i++) {
//    $sellersToInsert[] = [
//        $faker->name(),
//        $faker->hunger(),
//        $faker->thirst(),
//        $faker->sleep(),
//        $faker->fatigue(),
//        $faker->boredom(),
//        $faker->level(),
//        $faker->birthdate() . "" . $faker->time(),
//        $faker->deathdate() . "" . $faker->time(),
//        $faker->account_id(),
//    ];
//}
//
//$productsToInsert = [];
//for($i = 0; $i < $seedConfig["actions"]; $i++) {
//    $productsToInsert[] = [
//        $faker->id_tamagotchi(),
//        $faker->name(),
//    ];
////        $faker->word(),
////        $faker->text(),
////        $faker->randomFloat(2, 0, 999),
////        $faker->numberBetween(1, $seedConfig["clients"])
//}
//Database::bulkInsert("accounts", [
//    "name",
//], $clientsToInsert);
//
//Database::bulkInsert("tamagotchis", [
//    "name",
//    "hunger",
//    "thirst",
//    "sleep",
//    "fatigue",
//    "boredom",
//    "level",
//    "birthdate",
//    "deathdate",
//    "account_id",
//], $sellersToInsert);
//
//Database::bulkInsert("actions", [
//    "id_tamagotchi",
//    "name",
//], $productsToInsert);