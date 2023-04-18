import logging
import requests
import spotipy
from spotipy.oauth2 import SpotifyClientCredentials
from requests.adapters import HTTPAdapter
from requests.packages.urllib3.util.retry import Retry
import concurrent.futures

# Set up logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s %(levelname)s %(message)s')
logger = logging.getLogger(__name__)

# Set up the Spotify API credentials
client_id = '9aedf61619ce43f59fe24a7634dc847a'
client_secret = '7b8c89e9e1024c118606e6552ca658dd'
client_credentials_manager = SpotifyClientCredentials(client_id=client_id, client_secret=client_secret)
sp = spotipy.Spotify(client_credentials_manager=client_credentials_manager)

# Set up requests session
session = requests.Session()
retry_strategy = Retry(
    total=5,
    backoff_factor=1,
    status_forcelist=[429, 500, 502, 503, 504],
    allowed_methods=["HEAD", "GET", "PUT"],
)
adapter = HTTPAdapter(max_retries=retry_strategy)
session.mount("https://", adapter)
session.mount("http://", adapter)

# Define a function to update the artist's Spotify ID in the REST API
def update_spotify_id(artist):
    try:
        results = sp.search(q=artist['name'], type='artist', limit=1)
        items = results.get('artists', {}).get('items', [])
        if len(items) > 0:
            artist_id = items[0]['id']
            artist['spotify_id'] = artist_id
        else:
            artist['spotify_id'] = 'Not Found'

        # Use the PUT method to update the artist's Spotify ID in the REST API
        url = f'https://localhost/api/artists/{artist["id"]}'
        payload = {'spotifyId': artist['spotify_id']}
        headers = {'content-type': 'application/ld+json'}
        response = session.put(url, json=payload, headers=headers, verify=False)
        response.raise_for_status()
        logger.info(f"Updated Spotify ID for artist {artist['name']} with ID {artist['id']}")
    except Exception as e:
        logger.error(f"Failed to update artist {artist['name']} with ID {artist['id']}: {e}")

# Fetch artist names from the REST API
try:
    response = session.get('https://localhost/api/artists?exists[spotifyId]=false', headers={'accept': 'application/ld+json'}, verify=False)
    response.raise_for_status()
    response_json = response.json()
    artists = [{'name': member['name'], 'id': member['id']} for member in response_json['hydra:member']]
    logger.info(f"Found {len(artists)} artists without Spotify IDs")
except Exception as e:
    logger.error(f"Failed to fetch artists: {e}")
    raise

# Use threading to update the Spotify IDs for all artists in parallel
with concurrent.futures.ThreadPoolExecutor() as executor:
    futures = []
    for artist in artists:
        future = executor.submit(update_spotify_id, artist)
        futures.append(future)
    
    # Wait for all futures to complete
    for future in concurrent.futures.as_completed(futures):
        pass
