# 🌋 Volcanic by Group 3

Volcanic is a web application for exploring and tracking volcanic activity worldwide. It combines real-time data and interactive tools to provide an engaging experience for volcano enthusiasts.

<div align="center">
  <img src="Images/volcanic.png" alt="Volcanic App" width="100%">
</div>

## About the Project

Volcanic brings together volcano data, advanced search, interactive maps, and user achievements in one place. The app includes admin management tools, a real-time activity feed, and an AI chatbot when API keys are configured.

For local setup, database configuration, and API key requirements, see [SETUP.md](SETUP.md).

### 🔑 Login Credentials

Once the application is running and seeded, you can log in with the following credentials:

| Role | Username | Password |
|------|----------|----------|
| **Admin** | `admin` | `Volcanic!Demo#2026` |
| **User** | `MarioR` | `Volcanic!User#2026` |

## 🧪 Live Demo

Try the hosted demo here: https://volcanic.onrender.com/

### Demo features

- Log in as an admin or user, or create your own user
- Browse volcanoes, apply filters/sorting, and view profiles
- Use the admin dashboard to manage volcanoes, achievements, and users
- Explore the interactive map, chatbot, and real-time activity feed

### Demo limitations

- Demo data is periodically reset, and newly created accounts will not persist (ephemeral storage).
- The demo is limited to 100 requests per month per API (Ambee and Google Gemini), once a limit is reached, the related real-time activity or chatbot features will not work.

## Features and Ownership

| Feature | Contributor | Description |
| :--- | :--- | :--- |
| Admin Forms | [Carolina](https://github.com/chaeyrie) | Manage volcanoes, achievements, and users |
| Chatbot | [Gabriele](https://github.com/Gabbo693) | Interactive assistant using REST API |
| Real-Time Activity | [Luigi](https://github.com/Lucol24) | Integration of Ambee API for live data |
| Advanced Search | Manish | Sophisticated filtering for volcanoes |
| Interactive Map | [Lara](https://github.com/Lara-Ghi) | Custom Leaflet-based map with real-time status updates |
| Filter and Sorting System | [Mats](https://github.com/mqts241) | Filter or sort volcanoes by specific criteria |
