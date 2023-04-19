import logging
import requests
import spotipy
from spotipy.oauth2 import SpotifyClientCredentials
from requests.adapters import HTTPAdapter
from requests.packages.urllib3.util.retry import Retry
import concurrent.futures

from spotify_id_updater import update_spotify_id


def test_update_spotify_id_found_artist():
    artist = {'name': 'Radiohead', 'id': 123}
    update_spotify_id(artist)
    assert artist['spotify_id'] != 'Not Found'


def test_update_spotify_id_not_found_artist():
    artist = {'name': 'adazzrgfdzaedaz', 'id': 123}
    update_spotify_id(artist)
    assert artist['spotify_id'] == 'Not Found'