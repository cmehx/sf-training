import logging
import spotipy
import requests
from spotipy.oauth2 import SpotifyClientCredentials
import sys
# Set up logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Set up the Spotify API client
client_id = '9aedf61619ce43f59fe24a7634dc847a'
client_secret = '7b8c89e9e1024c118606e6552ca658dd'
client_credentials_manager = SpotifyClientCredentials(client_id=client_id, client_secret=client_secret)
sp = spotipy.Spotify(client_credentials_manager=client_credentials_manager)

# Set up the API endpoint and pagination variables
api_endpoint = 'https://localhost/api/artists'
page_number = 1

# Set up session
session = requests.Session()

# Loop through all pages of artists
    # Get artists data from the API
headers = {'content-type': 'application/ld+json', 'accept': 'application/ld+json'}
response = session.get(api_endpoint, headers=headers, verify=False)
    # If the response was successful, process the artists data
json_data = response.json()
artists = json_data.get('hydra:member', [])
for artist in artists:
    spotify_id = artist.get('spotifyId')
    artist_name = artist.get('name')
    artist_id = artist.get('id')
    albums_response = sp.artist_albums(spotify_id, album_type='album,single')
    if 'items' in albums_response:
        for album in albums_response['items']:
            release_spotify_id = album.get('id')
            release_name = album.get('name')
            release_artist = artist_name  # using the artist name from the artist data
            release_data = {
                "type": album.get('type'),
                "name": album.get('name'),
                "artist": artist.get("@id"),
                "spotifyId": album.get('id'),
                "popularity": album.get('popularity', 0),
                "releaseDate": album.get('release_date')
            }
            api_endpoint_r = 'https://localhost/api/releases'
            headers = {'content-type': 'application/ld+json', 'accept': 'application/ld+json'}
            response = session.post(api_endpoint_r, headers=headers, json=release_data, verify=False)
            if response.status_code == 201:
                logging.info(f"Successfully added {release_name} by {release_artist} to the releases database!")
            else:
                logging.warning("Failed to add release to the database.")
    else:
        logging.warning(f"No albums found for artist {artist_name}.")
