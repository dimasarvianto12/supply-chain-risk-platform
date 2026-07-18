# 🌐 Supply Chain Risk Intelligence Platform

Platform pemantauan risiko rantai pasok global berbasis multi-API dan analitik data. Sistem ini mengintegrasikan berbagai API gratis untuk menampilkan data cuaca, ekonomi, nilai tukar, berita, dan lokasi pelabuhan dalam satu dashboard interaktif.

---

## 🚀 Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| **Dashboard Utama** | Ringkasan total negara, rata-rata risiko, dan 5 negara dengan risiko tertinggi |
| **Country Dashboard** | Detail negara (GDP, inflasi, populasi, mata uang, cuaca, risiko) + tombol favorit |
| **Weather Monitoring Map** | Peta dunia interaktif dengan marker cuaca (suhu, kelembaban, angin) |
| **Currency Dashboard** | Tabel kurs terkini + grafik pergerakan 7 hari terakhir (Chart.js) |
| **News Intelligence** | Berita logistik/trade/shipping/economy dengan filter negara, sentimen, kata kunci |
| **Port Location Dashboard** | Peta pelabuhan dunia + daftar pelabuhan + pencarian berdasarkan nama/negara |
| **Data Visualization Dashboard** | 4 grafik tren: GDP, Inflasi, Kurs, dan Risiko |
| **Country Comparison Engine** | Bandingkan 2 negara (GDP, inflasi, kurs, cuaca, risiko) + grafik batang |
| **Favorite Monitoring List** | Simpan negara favorit untuk pemantauan cepat |
| **Admin Dashboard** | Kelola User, Data Pelabuhan, dan Artikel Analisis |

---

## 🛠️ Teknologi

| Lapisan | Teknologi |
|---------|-----------|
| **Backend** | Laravel 11 (PHP 8.2) |
| **Database** | MySQL |
| **Frontend** | Bootstrap 5, JavaScript ES6 |
| **Visualisasi** | Chart.js, Leaflet.js |
| **API Eksternal** | Open-Meteo, World Bank, REST Countries v5, ExchangeRate, GNews |
| **Deployment** | GitHub, Docker (opsional) |

---

## 📦 API Eksternal yang Digunakan

| API | Fungsi | API Key |
|-----|--------|---------|
| [Open-Meteo](https://open-meteo.com/) | Cuaca global (suhu, angin, hujan) | ❌ Tidak perlu |
| [World Bank](https://data.worldbank.org/) | GDP dan Inflasi per negara | ❌ Tidak perlu |
| [REST Countries v5](https://restcountries.com/) | Data negara (populasi, mata uang, bendera) | ✅ **Perlu** (gratis) |
| [ExchangeRate](https://www.exchangerate-api.com/) | Nilai tukar mata uang | ✅ **Perlu** (gratis) |
| [GNews](https://gnews.io/) | Berita logistik, trade, ekonomi | ✅ **Perlu** (gratis) |
| [OpenStreetMap](https://www.openstreetmap.org/) | Peta dasar (Leaflet.js) | ❌ Tidak perlu |

---

## 🔧 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/username/supply-chain-risk-platform.git
cd supply-chain-risk-platform