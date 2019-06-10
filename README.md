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

## Bot Rule Mode

AJN Framework Bot memiliki 2 mode untuk menjalankan bot. Rule yang dijalankan di kedua mode ini ada di file `botEngine.php`. Berikut adalah penjelasan singkat penggunaan rule mode AJN Framework Bot.

**1. Mode `Manual`** 

Mode Manual merupakan mode dimana eksekusi terhadap perintah dilakukan secara manual atau menggunakan pengecekkan secara koding manual oleh pemilik bot. Berikut contoh penggunaan mode manual

```
if($text=='start'){
	$app->send('Selamat Datang {{NAMA}}');
}elseif($text=='help'){
	$app->send("Daftar Perintah tersedia:\n/start - untuk memulai\n/help - untuk bantuan daftar perintah");
}else{
	$app->send('Maaf kami belum mengenali perintah Anda');
}
```

**2. Mode `Rule`**

Mode Rule merupakan mode dimana eksekusi terhadap perintah akan melakukan pencocokan kata (match word) secara otomatis dengan mengacu pada daftar rule yang sudah dibuat. Rule bisa dibuat dalam sebuah file ataupun tabel database. Khusus untuk mode `rule` file `botEngine.php` harus dibuat dalam bentuk class dengan nama `botEngine` yang memperluas (extends) dari class `AJN`. Rule yang dijalankan dalam bentuk method yang dipanggil di `callback` pada rule. Berikut contoh penggunaannya.

```
class botEngine extends AJN{
	function sapa_waktu($text){
		$e = explode(' ', $text);
		if($e[0]=='selamat' && $e[1]=='pagi')
			$output = "Selamat pagi {{NAMA}}";
		elseif($e[0]=='selamat' && $e[1]=='siang')
			$output = "Selamat siang {{NAMA}}";
		elseif($e[0]=='selamat' && $e[1]=='sore')
			$output = "Selamat sore {{NAMA}}";
		elseif($e[0]=='selamat' && $e[1]=='malam')
			$output = "Selamat malam {{NAMA}}";
		return $output;
	}	
}
```

## Environment Variabel

Di AJN Framework Bot, sudah tersedia beberapa environment variabel yang dapat langsung digunakan di `botEngine.php`. Beikut adalah daftar variabel dan fungsinya

**Global**

- `BASE_PATH` : memanggil lokasi full path atau alamat folder aplikasi
- `APP_PATH` : memanggil lokasi core aplikasi (folder `inc`)
- `DATA_PATH` : memanggil lokasi data aplikasi (folder `data`)

**Bot Engine 1 (var)**

- `$text` : isi text chat yang dikirim user setelah diparsing (dihapus simbol `/`, `!` dan `?`) di awal dan akhir chat serta sudah dikonversi menjadi huruf kecil semua
- `$message` : isi text chating seadanya yang dikirim user
- `$app` : object inti aplikasi, beberapa method yang tersedia `$app->send($isi_chat_balasan, $options)` untuk mengirim chat balasan ke user dengan parameter `$options` bersifat optional, `$app->user` untuk mengambil object informasi user
- `$chat` : mengambil raw data chat yang dikirim user

**Bot Engine 2 (data - khusus mode `rule`)**

- `{{WAKTU}}` - nama waktu sesuai jam (pagi, siang, sore, malam)
- `{{JAM}}` - jam (0, 9, 22, dll)
- `{{BULAN}}` - nama bulan (Januari, Februari, dll)
- `{{TAHUN}}` - angka tahun 4 digit (2019)
- `{{DATE}}` - tanggal full (5 Mei 2019)
- `{{HOUR}}` - jam (17:22)
- `{{DATE_NEXT}}` - tanggal full besok (6 Mei 2019)
- `{{DATE_PREV}}` - tanggal full kemarin (4 Mei 2019)
- `{{DATE_NEXT2}}` - tanggal full besok lusa (7 Mei 2019)
- `{{DATE_PREV2}}` - tanggal full kemarin lusa (3 Mei 2019)
- `{{DATE_NWEEK}}` - tanggal full minggu depan (12 Mei 2019)
- `{{DATE_LWEEK}}` - tanggal full minggu lalu (28 April 2019)
- `{{DAY}}` - nama hari (Minggu, Senin, Selasa, dst)
- `{{DAY_NEXT}}` - nama hari besok (Senin)
- `{{DAY_PREV}}` - nama hari kemarin (Sabtu)
- `{{DAY_NEXT2}}` - nama hari besok lusa (Selasa)
- `{{DAY_PREV2}}` - nama hari kemarin lusa (Jumat)
- `{{NAMA}}` - nama akun pengguna
- `{{ID}}` - ID akun pengguna
- `{{USERNAME}}` - username akun pengguna (jika ada)
- `{{NAMA_BOT}}` - nama bot (sesuai platform)
- `{{PLATFORM}}` - nama platform aktif
- `{{PERINTAH}}` - kata pertama dari chat user