<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Tarzı Müzik Çalar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #121212;
            color: white;
            text-align: center;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .player {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 15px;
            width: 350px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .cover {
            width: 200px;
            height: 200px;
            background: #333;
            margin: 10px auto;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }

        h2 {
            font-size: 18px;
            margin-top: 10px;
        }

        .controls {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        button {
            background-color: #1DB954;
            border: none;
            padding: 10px 15px;
            margin: 5px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            color: white;
            transition: 0.2s;
        }

        button:hover {
            background-color: #17a74a;
        }

        audio {
            display: none;
        }

        .playlist {
            margin-top: 20px;
            width: 80%;
            max-width: 350px;
            height: 150px; /* Sabit yükseklik */
            overflow-y: auto; /* Dikey kaydırma */
            border-radius: 10px;
            background: #1e1e1e;
            padding: 10px;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .playlist ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .playlist li {
            padding: 10px;
            border-bottom: 1px solid #333;
            cursor: pointer;
            transition: 0.2s;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .playlist li:hover {
            background-color: #222;
        }

        .playing {
            color: #1DB954;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="player">
            <div class="cover">
                <img id="album-cover" src="gorsel/gorsel.png" alt="Albüm Kapağı">
            </div>
            <h2 id="song-title">Şarkı Yükleniyor...</h2>
            <audio id="audio-player" controls></audio>
            <div class="controls">
                <button onclick="prevSong()">⏮</button>
                <button onclick="togglePlay()">▶️ / ⏸</button>
                <button onclick="nextSong()">⏭</button>
            </div>
        </div>

        <div class="playlist">
            <h3>Çalma Listesi</h3>
            <ul id="song-list"></ul>
        </div>
    </div>

    <script>
        let songs = [];
        let currentSongIndex = 0;
        const audioPlayer = document.getElementById("audio-player");
        const songTitle = document.getElementById("song-title");
        const songList = document.getElementById("song-list");

        async function loadSongs() {
            try {
                const response = await fetch("muzik/");
                const text = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, "text/html");

                songs = [...doc.querySelectorAll("a")]
                    .map(a => a.getAttribute("href"))
                    .filter(name => name.endsWith(".mp3"));

                if (songs.length > 0) {
                    currentSongIndex = 0;
                    updatePlaylist();
                    playSong();
                } else {
                    songTitle.innerText = "Müzik bulunamadı!";
                }
            } catch (error) {
                console.error("Şarkılar yüklenirken hata oluştu!", error);
                songTitle.innerText = "Şarkılar yüklenemedi!";
            }
        }

        function playSong(index = currentSongIndex) {
            if (songs.length === 0) return;
            currentSongIndex = index;
            audioPlayer.src = "muzik/" + songs[currentSongIndex];
            songTitle.innerText = songs[currentSongIndex].replace(/%20/g, " ");
            audioPlayer.play();
            highlightPlayingSong();
        }

        function togglePlay() {
            if (audioPlayer.paused) {
                audioPlayer.play();
            } else {
                audioPlayer.pause();
            }
        }

        function nextSong() {
            currentSongIndex = (currentSongIndex + 1) % songs.length;
            playSong();
        }

        function prevSong() {
            currentSongIndex = (currentSongIndex - 1 + songs.length) % songs.length;
            playSong();
        }

        function updatePlaylist() {
            songList.innerHTML = "";
            songs.forEach((song, index) => {
                let li = document.createElement("li");
                li.textContent = song.replace(/%20/g, " ");
                li.onclick = () => playSong(index);
                songList.appendChild(li);
            });
            highlightPlayingSong();
        }

        function highlightPlayingSong() {
            document.querySelectorAll(".playlist li").forEach((li, index) => {
                li.classList.toggle("playing", index === currentSongIndex);
            });
        }

        audioPlayer.addEventListener("ended", nextSong);
        loadSongs();
    </script>

</body>
</html>
