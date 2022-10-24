# mekayalar.com Spotify Now Playing API

With this API, an end-point is created for **the currently playing** feature on mekayalar.com.

## Usage
Requirements to use this API;

* A Spotify developer application created on the [Spotify Developer Dashboard](https://developer.spotify.com/dashboard/applications).
* We need the access and refresh tokens that you created for your spotify account through the application you created.

### Get Application Client ID and Client Secret

1. Go to the [Spotify Developer Dashboard](https://developer.spotify.com/dashboard/applications).
2. Click on the application you created.
3. Copy the Client ID and Client Secret.
4. Paste them into the `.env.example` file.
5. Rename the `.env.example` file to `.env`.

### Get Access Token & Refresh Token

You can see the details from [here](https://www.postman.com/merichrocks/workspace/aa4aa261-15b8-40b8-8b61-c0ad23137a3f/overview).

After you get the codes, you can place the codes on `cursor.json.example` and rename this file to `cursor.json`.


## That's it! You can now use the API. 🎉

** ups, I forgot, you should run the `composer dump-autoload` and `composer install` commands on the project directory.

## Endpoints

### GET `/json`
You can see the song playing on this endpoint, if the song is not playing, you can get a return accordingly.

#### Example Responses

_successfully response,_

```json
{
  "name": "Злой",
  "artists": [
    {
      "external_urls": {
        "spotify": "https://open.spotify.com/artist/55jryyk7RhvMbrvoF0ndBh"
      },
      "href": "https://api.spotify.com/v1/artists/55jryyk7RhvMbrvoF0ndBh",
      "id": "55jryyk7RhvMbrvoF0ndBh",
      "name": "SLAVA MARLOW",
      "type": "artist",
      "uri": "spotify:artist:55jryyk7RhvMbrvoF0ndBh"
    },
    {
      "external_urls": {
        "spotify": "https://open.spotify.com/artist/0Cm90jv892OeEegB3ELmvN"
      },
      "href": "https://api.spotify.com/v1/artists/0Cm90jv892OeEegB3ELmvN",
      "id": "0Cm90jv892OeEegB3ELmvN",
      "name": "Eldzhey",
      "type": "artist",
      "uri": "spotify:artist:0Cm90jv892OeEegB3ELmvN"
    }
  ],
  "is_playing": true,
  "url": "https://open.spotify.com/track/0Y5v9NeTZdPbPDMdXYDWdo"
}
```

_error response,_

```json
{
  "error": "error message",
  "is_playing": false
}
```


### GET `/auth`
to authorize the application and get `authorization_code`.

This endpoint simply guides you to the application with the correct parameters.