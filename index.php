<!DOCTYPE html>
<html>
<head>
    <title>Nomor Antrian</title>
</head>
<style>
    body{
        background:rgb(48, 148, 241);
    }
    .container{
        width: 680px;
        padding: 20px;
        margin: 50px auto;
        background:rgb(89, 233, 252);
        box-shadow: 0px 0px 20px #000;
        border: 2px solid rgb(154, 164, 201);
        border-radius: 20px;
        text-align: center;
    }
    .counter{
        width: 98%;
        border-radius: 5px;
        text-align: center;
        font-size: 20px;
        margin: 10px auto;
    }
    .navigasi{
        width: 49%;
        border-radius: 5px;
        margin: 2px auto;
        padding: 10px;
        background:rgb(67, 59, 184);
        font-size: 15px;
        font-weight: bold;
        color: white;
        cursor: pointer;
    }
    .bigNumber{
        font-size: 90px;    
    }
</style>
<body>
    <?php
    if (isset($_POST["navigasi"])) {
        $navigasi=$_POST["navigasi"];
        $nilai = $_POST["counter"];
    }
    ?>
    <?php
    function angkaKeAudio($angka, $pembatas = true) {
        $angka = intval($angka);
        $satuan = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan"];
        $audio = [];
    
        if ($pembatas && $angka > 0) {
            $audio[] = "antrian.mp3";
        }   
        
        // Proses triliun
        if ($angka >= 1000000000000) {
            $triliun = intval($angka / 1000000000000);
            $audio = array_merge($audio, angkaKeAudio($triliun, false));
            $audio[] = "triliun.mp3";
            $angka %= 1000000000000;
        }

        // Proses miliar
        if ($angka >= 1000000000) {
            $miliar = intval($angka / 1000000000);
            $audio = array_merge($audio, angkaKeAudio($miliar, false));
            $audio[] = "miliar.mp3";
            $angka %= 1000000000;
        }

        // Proses jutaan
        if ($angka >= 1000000) {
            $juta = intval($angka / 1000000);
            $audio = array_merge($audio, angkaKeAudio($juta, false));
            $audio[] = "juta.mp3";
            $angka %= 1000000;
        }
        
        // Proses ribuan
        if ($angka >= 1000) {
            $ribu = intval($angka / 1000);
            if ($ribu == 1) {
                $audio[] = "seribu.mp3";
            } else {
                $audio = array_merge($audio, angkaKeAudio($ribu, false));
                $audio[] = "ribu.mp3";
            }
            $angka %= 1000;
        }
    
        // Proses ratusan
        if ($angka >= 100) {
            $ratus = intval($angka / 100);
            if ($ratus == 1) {
                $audio[] = "seratus.mp3";
            } else {
                $audio[] = $satuan[$ratus] . ".mp3";
                $audio[] = "ratus.mp3";
            }
            $angka %= 100;
        }

        // Puluhan dan satuan
        if ($angka < 10) {
            $audio[] = $satuan[$angka] . ".mp3";
        } elseif ($angka == 10) {
            $audio[] = "sepuluh.mp3";
        } elseif ($angka == 11) {
            $audio[] = "sebelas.mp3";
        } elseif ($angka < 20) {
            $audio[] = $satuan[$angka - 10] . ".mp3";
            $audio[] = "belas.mp3";
        } elseif ($angka < 100) {
            $puluh = intval($angka / 10);
            $sisa = $angka % 10;
            $audio[] = $satuan[$puluh] . ".mp3";
            $audio[] = "puluh.mp3";
            if ($sisa > 0) {
                $audio[] = $satuan[$sisa] . ".mp3";
            }
        }
    
        return $audio;
    }
    
    
    $audioPath = './audio/';
    ?>
    <div class="container">   
        <form method="post" action="">
        <h1>Nomor Antrian</h1>
        <div>
        <input style="font-weight: bold;" type="text" name="counter" class="counter" value=
        <?php
        $nilai = @$_POST["counter"];
        if ($nilai==null) {
            echo "0";
        }
        if (isset($_POST["navigasi"])) {
            if ($navigasi=="prev") {
                echo $nilai = max(0, --$nilai);
            }
            elseif ($navigasi=="next") {
                echo ++$nilai;
            }
            elseif ($navigasi=="reset") {
                echo $nilai = 0;
            }
        }
        $audioList = angkaKeAudio($nilai);
        $fullPath = [];
        foreach ($audioList as $a) {
            $file = $audioPath . $a;
            if (file_exists($file)) {
                $fullPath[] = $file;
            }
        }
        ?>
        >
        </div>
        <div>
            <button name="navigasi" value="prev" class="navigasi">Prev</button>
            <button name="navigasi" value="next" class="navigasi">Next</button>
            <button name="navigasi" value="reset" class="navigasi">Reset</button>
            <button type="button" onclick="putarUlang()" class="navigasi">Putar Ulang</button>
        </div>
        <div class="bigNumber">
        <?php
        if ($nilai==null) {
            echo "0";
        }
        else {
            echo $nilai;
        }
        ?>
        </div>
        </form>
        <?php if (!empty($fullPath)) : ?>
        <audio id="suaraAntrian" style="display:none;"></audio>
        <script>
            const audioFiles = <?= json_encode($fullPath) ?>;
            let index = 0;
            const player = document.getElementById('suaraAntrian');

            function playNext() {
                if (index < audioFiles.length) {
                    player.src = audioFiles[index];
                    player.play();
                    index++;
                }
            }

            player.onended = playNext;
            window.onload = playNext;

            function putarUlang() {
                index = 0;
                playNext();
            }
        </script>
    <?php endif; ?>
    </div>
</body>
</html>