import axios from 'axios';
import QrScanner from 'qr-scanner';

const state = {
    token: null,
    sessionId: window?.sessionId,
};

async function getGeo() {
    if (!('geolocation' in navigator)) {
        return { lat: null, lng: null, confidence: 0 };
    }

    return new Promise((resolve) => {
        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                resolve({
                    lat: coords.latitude,
                    lng: coords.longitude,
                    confidence: coords.accuracy ? Math.min(1, 100 / coords.accuracy) : 0,
                });
            },
            () => resolve({ lat: null, lng: null, confidence: 0 }),
            { enableHighAccuracy: true, timeout: 8000 }
        );
    });
}

async function checkIn(token) {
    const geo = await getGeo();

    await axios.post('/attendance/check-in', {
        token,
        lat: geo.lat,
        lng: geo.lng,
        geo_confidence: geo.confidence,
    });

    state.token = token;
    startBeacons();
}

function startBeacons() {
    window.setInterval(async () => {
        if (!state.token) {
            return;
        }

        const geo = await getGeo();
        await axios.post(`/sessions/${state.sessionId}/beacon`, {
            token: state.token,
            lat: geo.lat,
            lng: geo.lng,
            geo_confidence: geo.confidence,
            missed: document.hidden,
        });
    }, 120000);
}

async function requestKeyword() {
    const { data } = await axios.post(`/trainer/sessions/${state.sessionId}/challenge`);
    document.getElementById('keyword-display').textContent = data.keyword;
}

async function submitKeyword() {
    const value = document.getElementById('keyword').value.trim().toUpperCase();
    await axios.post(`/sessions/${state.sessionId}/challenge`, { keyword: value });
    document.getElementById('challenge-section').classList.add('hidden');
}

function initQrScanner() {
    const video = document.createElement('video');
    video.classList.add('hidden');
    document.body.appendChild(video);

    const scanner = new QrScanner(video, async (result) => {
        await checkIn(result.data);
        scanner.stop();
        video.remove();
    });

    scanner.start();
}

function bindEvents() {
    document.getElementById('generate-keyword')?.addEventListener('click', requestKeyword);
    document.getElementById('submit-keyword')?.addEventListener('click', submitKeyword);
    document.getElementById('scan-qr')?.addEventListener('click', initQrScanner);
    document.getElementById('join-remote')?.addEventListener('click', () => {
        document.getElementById('challenge-section').classList.remove('hidden');
    });
}

bindEvents();
