const express = require('express');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');

puppeteer.use(StealthPlugin());

const app = express();
const port = 3001;

app.use(express.json());

app.post('/scrape', async (req, res) => {
  const { secret_key, url, selectorsArray, attributesArray, namesArray, postSelector } = req.body;

  if (secret_key !== 'test') {
    return res.status(401).json({ error: 'Unauthorized' });
  }

  try {
    const browser = await puppeteer.launch({ headless: false });
    const page = await browser.newPage();

    await page.goto(url, {timeout: 60000});

    let results = [];

    while (true) {
      await page.evaluate(() => {
        window.scrollBy(0, window.innerHeight);
      });

      try {
        await page.waitForTimeout(3000);
      } catch (error) {
        break;
      }

      const posts = await page.$$(postSelector);
      console.log(`Found ${posts.length} posts.`);  // Print the number of posts

      for (const post of posts) {
        const itemData = {};

        for (let i = 0; i < selectorsArray.length; i++) {
          const selector = selectorsArray[i];
          const attribute = attributesArray[i];
          const name = namesArray[i];

          try {
            const elements = await post.$$(selector);
            let values = [];

            for (let element of elements) {
              let value;

              if (attribute === 'innerText') {
                value = await element.evaluate(el => el.innerText);
              } else {
                value = await element.evaluate((el, attr) => el.getAttribute(attr), attribute);
              }

              values.push(value);
            }

            itemData[name] = values;

          } catch (error) {
            console.error(`Error retrieving data for selector "${selector}": ${error.message}`);
          }
        }

        results.push(itemData);
      }

      let currentHeight = await page.evaluate('document.body.scrollHeight');
      let viewportHeight = await page.evaluate('window.innerHeight');
      let scrollPosition = await page.evaluate('window.scrollY');

      if (currentHeight <= viewportHeight + scrollPosition) {
        break;
      }
    }

    await browser.close();

    res.json({ results });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});

app.listen(port, () => {
  console.log(`Server is listening on port ${port}`);
});