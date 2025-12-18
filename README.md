### Hexlet tests and linter status:
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Kromian1_php-project-48&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=Kromian1_php-project-48)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Kromian1_php-project-48&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=Kromian1_php-project-48)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Kromian1_php-project-48&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=Kromian1_php-project-48)
[![Actions Status](https://github.com/Kromian1/php-project-48/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/Kromian1/php-project-48/actions)
[![check-project.yml](https://github.com/Kromian1/php-project-48/actions/workflows/check-project.yml/badge.svg)](https://github.com/Kromian1/php-project-48/actions/workflows/check-project.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Kromian1_php-project-48&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=Kromian1_php-project-48)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=Kromian1_php-project-48&metric=bugs)](https://sonarcloud.io/summary/new_code?id=Kromian1_php-project-48)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=Kromian1_php-project-48&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=Kromian1_php-project-48)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Kromian1_php-project-48&metric=coverage)](https://sonarcloud.io/summary/new_code?id=Kromian1_php-project-48)
[![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=Kromian1_php-project-48&metric=ncloc)](https://sonarcloud.io/summary/new_code?id=Kromian1_php-project-48)

# Вычислитель отличий

Утилита для сравнения содержимого текстовых файлов, также доступная в качестве библиотеки.
Поддерживаются как плоские, так и многоуровневые файлы.
Программа написана на PHP.

##  Установка

В качестве CLI-утилиты:

- git clone https://github.com/Kromian1/php-project-48.git
- cd php-project-48
- make install

## Требования

- PHP 8.3
- Composer
  
##  Запуск

Для запуска утилиты неообходимо запустить bin-файл gendiff, при необходимости указать формат, а также указать пути до двух сравниваемых файлов.

Пример запуска из корня программы:

- ./bin/gendiff --format plain ~/files/file1.json ~/files/file2.yaml

Для использования в качестве библиотеки:

- use function Differ\Differ\genDiff;

Сигнатура:

- genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string

Функция принимает на вход пути до файлов в строковом типе данных, а также формат вывода. По умолчанию используется stylish.

##  Опции

Показать документацию:

- ./bin/gendiff -h || ./bin/gendiff --help
  
Показать версию программы:

- ./bin/gendiff -v || ./bin/gendiff --version
  
Выбор формата вывода:
- --format <fmt>

вместо \<fmt\> указать формат.

## Форматы

Программа может читать файлы следующих форматов:

_JSON, YAML, YML._

## Вывод

Вывод программы доступен в следующем виде:

- Stylish. Используется как формат по умолчанию. Каждая строка имеет префикс, описывающий отличие.
   
Строки имееют следующие значения:

"-" - ключ есть в первом файле, но отсутствует во втором файле.

"+" - ключ отсутсвует в первом файле, но есть во втором файле, либо есть в обоих файлах, но с разными значениями.

" " - ключ есть в обоих файлах, и его значения совпадают.

- Plain. В текстовом виде описывает такие отличия ключей, как удаление, добавление, изменение.

- Json. В формате Json добавляет ключ, описывающий следующие действия: удаление, добавление, изменение, неизменение.

## Пример использования утилиты (Asciinema):

**Сравнение JSON**: https://asciinema.org/a/ifijK7SbAiWPhu2gpl1ZRrvbZ

**Сравнение YAML**: https://asciinema.org/a/ImRSPqJDCyYOrzbV51bgxXzte

**Сравнение с вложенными структурами**: https://asciinema.org/a/y7jfhp512gwVZTWEKh8BdOACt

**Сравнение с явным выбором формата вывода stylish**: https://asciinema.org/a/Vg3HSNXiVe4wV1HEF6Ppa6mEm

**Сравнение с явным выбором формата вывода Plain**: https://asciinema.org/a/tvPzADTuFjZZ5fagvUhc62qE6

**Сравнение с явным выбором формата вывода json**: https://asciinema.org/a/Az2V18BbYjSkZVA2Ik3sLUcUY
