<?php

namespace carono\yii2installer\commands;

use carono\yii2installer\ConsoleCheckBox;
use carono\yii2installer\InstallController;
use Yii;
use yii\db\Migration;

class CaronoController extends InstallController
{
    public function getMenu()
    {
        $gitignore = join(DIRECTORY_SEPARATOR, [\Yii::getAlias('@app'), '.gitignore']);
        $array = [
            [
                "text"     => "Copy .htaccess to @app/web",
                "checked"  => true,
                "disabled" => function (ConsoleCheckBox $item) {
                    $dir = [\Yii::getAlias('@app'), 'web', '.htaccess'];
                    return file_exists(join(DIRECTORY_SEPARATOR, $dir));
                },
                "exec"     => function (ConsoleCheckBox $item) {
                    $source = [\Yii::getAlias('@vendor'), 'carono', 'yii2-components', 'templates', '.htaccess'];
                    $dist = [\Yii::getAlias('@app'), 'web', '.htaccess'];
                    return copy(join(DIRECTORY_SEPARATOR, $source), join(DIRECTORY_SEPARATOR, $dist));
                }
            ],
            [
                "text"     => "Add db.php to @app/.gitignore",
                "checked"  => true,
                "disabled" => function (ConsoleCheckBox $item) use ($gitignore) {
                    $lines = explode("\n", file_get_contents($gitignore));
                    return array_search('db.php', $lines) || array_search('/config/db.php', $lines);
                },
                "exec"     => function (ConsoleCheckBox $item) use ($gitignore) {
                    file_put_contents($gitignore, "\n/config/db.php", FILE_APPEND);
                    return true;
                },
                "error"    => !file_exists($gitignore)
            ],
            [
                "text"     => "Create currency table",
                "checked"  => true,
                "disabled" => function (ConsoleCheckBox $item) {
                    return (bool)Yii::$app->db->getTableSchema('currency');
                },
                "exec"     => self::migrate('@carono/migrations/m151216_093214_currency')
            ],
            [
                "text"     => "Create cities table",
                "id"       => "cities",
                "checked"  => true,
                "disabled" => (bool)Yii::$app->db->getTableSchema('city'),
                "exec"     => self::migrate('@carono/migrations/m151216_084006_cities')
            ],
            [
                "text"     => "Create RBAC (@yii/rbac/migrations)",
                "checked"  => true,
                "disabled" => function (ConsoleCheckBox $item) {
                    return (bool)Yii::$app->db->getTableSchema('auth_item');
                },
                "exec"     => self::migrate('@yii/rbac/migrations')
            ],
            [
                "text"     => "Create 'file_upload' table",
                "checked"  => true,
                "disabled" => (bool)Yii::$app->db->getTableSchema('file_upload'),
                "exec"     => self::migrate('@carono/migrations/m151127_104851_file_upload_table'),
            ],
            [
                "text"     => "Create 'company' table (RUS)",
                "checked"  => true,
                "disabled" => (bool)Yii::$app->db->getTableSchema('company'),
                "exec"     => self::migrate('@carono/migrations/m160222_202733_company'),
            ],
            [
                "text"     => "Create 'User' table",
                "id"       => "user",
                "checked"  => true,
                "disabled" => (bool)Yii::$app->db->getTableSchema('user'),
                "exec"     => function (ConsoleCheckBox $item) {
                    $command = new Migration();
                    $table = [
                        'user'    => [
                            'id'              => $command->primaryKey(),
                            'login'           => $command->string(),
                            'hash'            => $command->string(),
                            'activation_code' => $command->string(),
                            'recover_code'    => $command->string(),
                            'last_logon'      => $command->dateTime(),
                            'access_token'    => $command->string(),
                            'created'         => $command->dateTime(),
                            'updated'         => $command->dateTime(),
                            'active'          => $command->boolean()->notNull()->defaultValue(false),
                        ],
                        'address' => [
                            'id'                => $command->primaryKey(),
                            'raw'               => $command->string(),
                            'postcode'          => $command->string(),
                            'street'            => $command->string(),
                            'build'             => $command->string(),
                            'structure'         => $command->string(),
                            'flat'              => $command->string(),
                            'registration_date' => $command->date(),
                        ]
                    ];
                    if ($item->owner->findById('cities')->value) {
                        $table["address"]["city_id"] = [$command->integer(), 'city', 'id'];
                    }
                    if ($item->findById('photos')->value) {
                        $table["user"]["photos"] = [$command->pivot(), 'file_upload', 'id', 'photo_id'];
                    }
                    if ($item->findById('personal')->value) {
                        $table["user"]["personal_id"] = [$command->integer(), 'personal', 'id'];
                        $table["personal"] = [
                            'id'               => $command->primaryKey(),
                            'email'            => $command->string(),
                            'first_name'       => $command->string(),
                            'second_name'      => $command->string(),
                            'patronymic'       => $command->string(),
                            'birth_date'       => $command->date(),
                            'sex'              => $command->boolean(),
                            'phone'            => $command->string(),
                            'avatar_id'        => [$command->integer(), 'file_upload', 'id'],
                            'legal_address_id' => [$command->integer(), 'address', 'id'],
                            'real_address_id'  => [$command->integer(), 'address', 'id'],
                            'updated'          => $command->dateTime()
                        ];
                    } else {
                        $table["user"] = array_merge(
                            $table["user"], [
                                'email'            => $command->string(),
                                'first_name'       => $command->string(),
                                'second_name'      => $command->string(),
                                'patronymic'       => $command->string(),
                                'birth_date'       => $command->date(),
                                'sex'              => $command->boolean(),
                                'phone'            => $command->string(),
                                'legal_address_id' => [$command->integer(), 'address', 'id'],
                                'real_address_id'  => [$command->integer(), 'address', 'id'],
                            ]
                        );
                    }
                    $index = [
                        ["user", "login", true],
                        ["user", "access_token", true],
                    ];
                    $command->upTables($table);
                    $command->upFk($command->collectFks($table));
                    $command->upIndex($index);
                    return true;
                },
                "items"    => [
                    [
                        "id"      => "personal",
                        "text"    => "User profile as relation",
                        "checked" => true,
                        "inherit" => true
                    ],
                    [
                        "id"      => "photos",
                        "text"    => "Create with photos",
                        "checked" => true,
                        "inherit" => true
                    ],
                    [
                        "id"      => "user_model",
                        "text"    => "Copy default User model",
                        "checked" => false,
                        "exec"    => function () {
                            $source = [
                                \Yii::getAlias('@vendor'),
                                'carono',
                                'yii2-components',
                                'templates',
                                'User.php'
                            ];
                            $dist = [\Yii::getAlias('@app'), 'models', 'User.php'];
                            return copy(join(DIRECTORY_SEPARATOR, $source), join(DIRECTORY_SEPARATOR, $dist));
                        }
                    ]
                ]
            ],
        ];
        return $array;
    }
}