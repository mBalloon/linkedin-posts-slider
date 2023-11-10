import requests

# This is a simulation of the POST request you would send in your PHP code.
# Replace with your actual endpoint and POST data structure.

# Endpoint URL
url = "https://scrape-js.onrender.com/scrape"

# POST data to be sent to the endpoint
data = {
    "secret_key": "test",
    "url": "https://www.linkedin.com/company/alpine-laser/",
    "postSelector": 'li[class="mb-1"]',
    "selectorsArray": [
        \'li[class="mb-1"] article\',
        \'time\',
        \'span[data-test-id="social-actions_reaction-count"]\',
        \'a[data-id="social-actions_comments"]',
        \'a[data-tracking-control-name="organization_guest_main-feed-card_feed-actor-name"]\'
    ],
    "attributesArray": ["data-activity-urn","innerText","innerText","innerText","innerText"],
    "namesArray": ["URN","age","reactions","comments","company-name"]
}

# Make the POST request and get the response
response = requests.post(url, json=data)

# Check if the request was successful
if response.status_code == 200:
    # The request was successful, print the response text
    print(response.text)
else:
    # There was an error, print the status code and response text
    print(f"Error: {response.status_code}")
    print(response.text)
