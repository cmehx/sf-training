import requests
import spotipy
from spotipy.oauth2 import SpotifyClientCredentials
import logging

logging.basicConfig(filename='artist_creation.log', level=logging.INFO, format='%(asctime)s:%(levelname)s:%(message)s')

# Set up the Spotify API credentials
client_id = '9aedf61619ce43f59fe24a7634dc847a'
client_secret = '7b8c89e9e1024c118606e6552ca658dd'
client_credentials_manager = SpotifyClientCredentials(client_id=client_id, client_secret=client_secret)
sp = spotipy.Spotify(client_credentials_manager=client_credentials_manager)

# Set up the API endpoint for creating artists
api_endpoint = 'https://localhost/api/artists'

# Define a function to fetch hip hop artists from Spotify and create them in the database
def fetch_and_create_hip_hop_artists():
    # Use the Spotify API to search for hip hop artists
    results = sp.search(q='genre:"%mumble rap%"', type='artist')
    artists = results['artists']['items']
    print(f"Found {len(artists)} artists.")
    
    # Get the next page of results, if available
    while results['artists']['next']:
        results = sp.next(results['artists'])
        artists.extend(results['artists']['items'])

    # Loop over each artist and create a new entry in the database via a POST request to the API endpoint
    with requests.Session() as session:
        for artist in artists:
            # Prepare the data for the POST request
            data = {
                'name': artist['name'],
                'spotifyId': artist['id']
            }
            # Make the POST request to create the artist in the database
            try:
                headers = {'content-type': 'application/ld+json', 'accept': 'application/ld+json'}
                response = session.post(api_endpoint, headers=headers, json=data, verify=False)
                response.raise_for_status()
            except requests.exceptions.RequestException as e:
                logging.error(f"Failed to create artist '{data['name']}' with ID '{artist['id']}' in the database. Error: {e}")
            else:
                if response.status_code == 201:
                    logging.info(f"Created artist '{data['name']}' with ID '{artist['id']}' in the database.")
                else:
                    logging.warning(f"Failed to create artist '{data['name']}' with ID '{artist['id']}' in the database. Status code: {response.status_code}")

# Call the function to fetch and create hip hop artists
fetch_and_create_hip_hop_artists()