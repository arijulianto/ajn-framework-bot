# AJN Framework Bot

## Framework Bot Multiplatform

AJN Framework Bot dibangun dengan kemampuan cross platform. Dalam hal ini, platform yang dimaksud adalah social messaging atau instant messaging.

**PENTING**: Perlu diingatkan bahwa AJN Framework Bot ini masih tahapan versi beta, jadi secara kinerja mungkin akan menemukan sebuah bug atau fitur yang belum lengkap.

## Platform yang Didukung

Saat ini platform yang didukung AJN Framework Bot adalah Telegram, Messenger dan Line.

## Konfigurable

AJN Framework Bot bersifat konfigurable, hal ini disesuaikan dengan kebutuhan dan cara kerja. Beberapa pengaturannya adalah:

* Mode: pilih `manual` jika ingin mendefinisikan sendiri, atau `rule` jika kondisi dan output dibuat dalam sebuah kerangka rule.
* Source: pilih `db` jika rule disimpan pada tabel di database, atau `file` jika rule disimpan dalam sebuah file JSON
* Source Name: jika `source` memilih `db` isi source name dengan nama tabel, atau nama file jika memilih source berupa file
* DB: definisikan informasi database jika menggunakan membutuhkan akses ke database, `db` mencakup `host`, `user`, `password` dan `name` (nama database)