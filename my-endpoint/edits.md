i want to edit the page created in the `options-page.php` to view a sample post with the bekow html and css from `public/styles.css` and some form fields that will manage its style while instantly reflecting the new style changes to the slider whenever any value of the fields changes
the feilds to manage :
1. Company info section (class: .section-company):
    - color: color picker (default: #454545)
    - font-size: number feild with increment and decrement buttons (default: 16px)
    - font-family: font family selector (default: "Titillium Web")
    - line-height: line height selector (default: 21px)

2. Author username and date section (class: .section-author-date):
    - color: color picker (default: #454545)
    - font-size: number feild with increment and decrement buttons (default: 14px)
    - font-family: font family selector (default: "Titillium Web")
    - font-weight: number feild with increment and decrement buttons (default: 300)
    - line-height: line height selector (default: 18px)

3. Post text section (class: .section-body):
    - color: color picker (default: #adb5bd)
    - font-size: number feild with increment and decrement buttons (default: 16px)
    - font-family: font family selector (default: "Titillium Web")
    - -webkit-line-clamp: (default: 5)

4. Post interactions section (class: .section-interactions): 
    - color: color picker (default: #454545)
    - font-size: number feild with increment and decrement buttons (default: 14px)
    - font-family: font family selector (default: "Titillium Web")
    - font-weight: number feild with increment and decrement buttons (default: 300)
    - line-height: line height selector (default: 18px)


    <div class="li-icon-white">
  <svg
    style="
      width: 30px;
      height: 30px;
      overflow: visible;
      fill: rgb(255, 255, 255);
    "
    viewBox="0 0 448 512"
  >
    <path
      d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"
    ></path>
  </svg>
</div>
<div class="img-container">
  <div
    class="li-single-img"
    style="
      background-image: url('https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_800/0/1696948113736?e=1700697600&v=beta&t=keJyTShAaigbh_J5MNMW6ZZKkM1WwZY58ajF0vkf-O4');
    "
  ></div>
</div>

<div class="info-container">
  <div
    class="li-author-img"
    style="
      background-image: url('https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1705536000&v=beta&t=9PVwxirZIj7Pgh68ihS7bA_UscLia5XiJy9llH9Q_PA');
    "
  ></div>
  <div class="section-company section-company">Alpine Laser</div>
  <div class="section-author-date">
    <span class="li-author-username">@alpine-laser . </span>
    <span class="li-post-age">3w ago</span>
  </div>
  <p class="section-body">
    Come see a live demo of femtosecond tube cutting today and tomorrow at MDM
    in booth 2803!
  </p>
  <div class="section-interactions">
    <span
      ><svg
        style="
          width: 24px;
          height: 24px;
          overflow: visible;
          fill: rgb(0, 122, 255);
        "
        viewBox="0 0 24 24"
      >
        <path fill="none" d="M0 0h24v24H0z"></path>
        <path
          d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm5.9 8.3-2.1 4.9c-.22.51-.74.83-1.3.8H9c-1.1 0-2-.9-2-2v-5c-.02-.38.13-.74.4-1L12 5l.69.69c.18.19.29.44.3.7v.2L12.41 10H17c.55 0 1 .45 1 1v.8c.02.17-.02.35-.1.5z"
          opacity=".3"
        ></path>
        <path
          d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"
        ></path>
        <path
          d="M17 10h-4.59l.58-3.41v-.2c-.01-.26-.12-.51-.3-.7L12 5l-4.6 5c-.27.26-.42.62-.4 1v5c0 1.1.9 2 2 2h5.5c.56.03 1.08-.29 1.3-.8l2.1-4.9c.08-.15.12-.33.1-.5V11c0-.55-.45-1-1-1z"
        ></path></svg
    ></span>
    <span class="li-post-reactions">${post.reactions} . </span>
    <span class="li-post-comments">${post.comments} comments</span>
  </div>
</div>
