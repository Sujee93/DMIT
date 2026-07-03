# DMIT — Insulin Resistance Tracker

A minimal, installable PWA for tracking weight, meals, workouts and daily walking habits. Built with React, TypeScript, Vite and Tailwind CSS.

All data is stored locally on-device (`localStorage`) — nothing is sent to a server.

## Install on iPhone

1. Open the live site in Safari.
2. Tap the Share icon, then **Add to Home Screen**.
3. Launch it from your Home Screen — it runs full-screen, works offline, and updates automatically when you're online.

## Develop

```bash
npm install
npm run dev
```

## Build

```bash
npm run build
```

Deploys automatically to GitHub Pages on push to `main` via `.github/workflows/deploy.yml`.
