import requests

# Define the URL of the Node.js server
url = "http://localhost:3001/scrape"

# Define the data to be sent in the POST request
data = {
    "secret_key": "test",
    "url": "https://www.linkedin.com/company/alpine-laser",
    "postSelector": 'li[class="mb-1"]',
    "selectorsArray": ['p[data-test-id="main-feed-activity-card__commentary"]'],
    "attributesArray": ['innerText'],
    "namesArray": ["posttext"]
}

try:
    # Make the POST request
    response = requests.post(url, json=data)

    # Print the response
    print(response.json())
except requests.exceptions.RequestException as e:
    print("An error occurred during the API call:", str(e))