<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

function linkedin_posts_slider_create_table()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'linkedin_posts';
  $charset_collate = $wpdb->get_charset_collate();
  $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        url text NOT NULL,
        author text NOT NULL,
        username text NOT NULL,
        age text NOT NULL,
        profilePicture text NOT NULL,
        copy text NOT NULL,
        images text NOT NULL,
        reactions int NOT NULL,
        comments int NOT NULL,
        synced boolean NOT NULL,
        published boolean NOT NULL,
        post_order int NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

  // JSON data from the file
  $json_data = <<<'EOT'
      [
        {
          "Position": "1",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "age": "1mo •",
          "copy": "Announcing the MediSCAN Pro - Alpine Laser's latest high performance laser processing workstation optimized for medical device manufacturing!\n\nThe configuration shown here features programmable XYZ motion coupled with a Scanlab precSYS 5-axis #micromachining galvo and a TRUMPF 2230 ultra short pulsed 515nm green #laser source and coaxial vision.\n\nThis machine was designed to process very fine features and complex holes in hard to machine polymer materials. (Shown are 0.25mm holes in a 1mm Pellethane tube)\n\nOther configurations of this workstation can be optimized for flat sheet cutting, traditional 2D galvo applications, marking, complex ablation, to name a few.\n\nContact sales@alpinelaser.com for more information.\n\nSCANLAB GmbH\nTRUMPF North America\n#medicaldevicemanufacturing",
          "images": [
            "https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_800/0/1695314437373?e=1700697600&v=beta&t=slwjjR_eHPJPHLveIXf24XLpNRp32hy41phrEB_pMyY",
            "https://media.licdn.com/dms/image/D5622AQGu92JK888ZUw/feedshare-shrink_800/0/1695314437386?e=1700697600&v=beta&t=Zf7xMoDtwBTCN905mseXz8rk77dtmfOSm08Tfh7qUUI",
            "https://media.licdn.com/dms/image/D5622AQFevdEZ-d2RfQ/feedshare-shrink_800/0/1695314436856?e=1700697600&v=beta&t=5kRgmLzLb9VPGUlMPWnTGO79_n0hlqW7DhUVwQzs-zQ",
            "https://media.licdn.com/dms/image/D5622AQGfdzbosfaiPw/feedshare-shrink_800/0/1695314437494?e=1700697600&v=beta&t=og3iW9NjIz2VSbFj4aUi385BLsxLLuIZ2MmXvuAe4Ck",
            "https://media.licdn.com/dms/image/D5622AQE9oTsaKKVG9A/feedshare-shrink_800/0/1695314437828?e=1700697600&v=beta&t=eUkg72s4keVlwaJ0QjqK5cz2Pk9LltlbcXA6wY3CizU"
          ],
          "reactions": "116",
          "comments": "8 comments"
        },
        {
          "Position": "3",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "age": "1w •",
          "copy": "Come see a live demo of femtosecond tube cutting today and tomorrow at MDM in booth 2803!",
          "images": [
            "https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_800/0/1696948113736?e=1700697600&v=beta&t=keJyTShAaigbh_J5MNMW6ZZKkM1WwZY58ajF0vkf-O4"
          ],
          "reactions": "20",
          "comments": "1 comment"
        },
        {
          "Position": "5",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "age": "3mo •",
          "copy": "Just completed the installation of two femtosecond laser tube cutting workstations paired with bar feeders and custom Alpine automated part extractors enabling this customer to run catheter shaft production lights out.\n\nContact the team at Alpine Laser today to see how we can help you transform your laser cutting operation.\n\nsales@alpinelaser.com",
          "images": [
            "https://media.licdn.com/dms/image/D5622AQE0uiOv1X59Og/feedshare-shrink_800/0/1689108312570?e=1700697600&v=beta&t=eJ1Ntg5tN2cqRJ--r5sJcHbaLCGW60wGlbWvl5OAZH8",
            "https://media.licdn.com/dms/image/D5622AQEDvNoAXKgCkA/feedshare-shrink_800/0/1689108308231?e=1700697600&v=beta&t=1soEvuOe2pQNSHGwxWPl5jPdBttmoM3T8rQm_Myxkss",
            "https://media.licdn.com/dms/image/D5622AQGuLM3G0lYTmQ/feedshare-shrink_800/0/1689108310054?e=1700697600&v=beta&t=KBIg0S6fPTpsgfDzvY5jx5mWh6EEU4AoLCQPG7y_n0Q",
            "https://media.licdn.com/dms/image/D5622AQEs3FWPkEZ4fg/feedshare-shrink_800/0/1689108313262?e=1700697600&v=beta&t=HoJuuTrLQWy4iZXrvMVoIv1wBgPUN1nYBk34XYSGUjA",
            "https://media.licdn.com/dms/image/D5622AQGwIi2isOxGuQ/feedshare-shrink_800/0/1689108311592?e=1700697600&v=beta&t=WvJ4ZE6Lk0KpjnWv-9iAs8Ix8aRAA9DYHr3SC3zdnhY"
          ],
          "reactions": "108",
          "comments": "5 comments"
        },
        {
          "Position": "6",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "age": "3mo •",
          "copy": "Need cuts with no heat affected zone and very clean edges? Take a look at these sample parts cut with the Alpine Laser Medicut Pro workstation utilizing a top of the line ultra short pulse femtosecond laser from TRUMPF.",
      
          "images": [
            "https://media.licdn.com/dms/image/D4D22AQGqLOmYU5zQJQ/feedshare-shrink_800/0/1689258424335?e=1700697600&v=beta&t=uP8Ie76uxvmOw9ahFB3slq595VwceCZnTBhObQLgGkM",
            "https://media.licdn.com/dms/image/D4D22AQFjeXMtn0ZgcQ/feedshare-shrink_800/0/1689258424269?e=1700697600&v=beta&t=v7XNtnlThPCVqQm4mYP_-0eKuWfLRkqwBQUMbXuzlxw",
            "https://media.licdn.com/dms/image/D4D22AQECZgYGzGDO6g/feedshare-shrink_800/0/1689258424307?e=1700697600&v=beta&t=uWSERibQHlagEnUZjWzktamM9FH97kBC3qjwN82N9Rw",
            "https://media.licdn.com/dms/image/D4D22AQEMYp-_RwB6hA/feedshare-shrink_800/0/1689258424267?e=1700697600&v=beta&t=2m71oyvQM6TdvYzUCyAHTVZ15j08UB2X58FNfi2TVSE"
          ],
          "reactions": "120",
          "comments": "6 comments"
        },
        {
          "Position": "8",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "age": "9mo •",
          "copy": "* Femtosecond Workstation Spotlight *\n\n- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source\n- Hollow Core Fiber Delivery with Active Beam Management\n- Laser control module and laser head unit mounted within the machine base\n- Available in both programmable 2 and 4 axis configurations\n\nInquire to learn more at sales@alpinelaser.com",
          "images": [
            "https://media.licdn.com/dms/image/C5622AQG3G4m1HdBRTQ/feedshare-shrink_800/0/1674590456558?e=1700697600&v=beta&t=k5YtvgDRkv5WaSn1dHoYUCeUv0cTuOOxRMGtQvZXWSg",
            "https://media.licdn.com/dms/image/C5622AQHltS4_M21yfQ/feedshare-shrink_800/0/1674590456620?e=1700697600&v=beta&t=XjGMMnhIUNUcXz6xMwiUb-T9Aq1608FNLQ-_XARboyk",
            "https://media.licdn.com/dms/image/C5622AQGKlqfHEc9TVA/feedshare-shrink_800/0/1674590456761?e=1700697600&v=beta&t=sBYFSRv1aWvisfv-sTsyx5wantSgUJ5FkvQoKwwuFzc",
            "https://media.licdn.com/dms/image/C5622AQEw2Fhe4KSHUA/feedshare-shrink_800/0/1674590456630?e=1700697600&v=beta&t=YyVctzHbooMWL4sntKLUFTcQSAWjYtQ_PZz0VqSUbU8"
          ],
      
          "reactions": "28",
          "comments": "0 comments"
        },
        {
          "Position": "14",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "age": "10mo •",
          "copy": "",
          "images": [
            "https://media.licdn.com/dms/image/C5622AQHvYERDghz__Q/feedshare-shrink_800/0/1672680058000?e=1700697600&v=beta&t=KoWwS3CnANMtkSZ06pxmG2s65N7xpsNiBCnJWcAVMZU",
            "https://media.licdn.com/dms/image/C5622AQGWbi5H_tF4dg/feedshare-shrink_800/0/1672680057456?e=1700697600&v=beta&t=slCQsGtSrTuAqbVmCIc8sVdbBjIjH07isIh_sm1wJhU",
            "https://media.licdn.com/dms/image/C5622AQFZhAkBS2MxdA/feedshare-shrink_800/0/1672680058001?e=1700697600&v=beta&t=ot7tquGj7lteSX-Jr4M15CQIi9v-2UtxrTTVIKNKzVg",
            "https://media.licdn.com/dms/image/C5622AQE27JYCc571qg/feedshare-shrink_800/0/1672680055708?e=1700697600&v=beta&t=mG6FcHD0oxRtzD823l9eBITM5XtVhyL1UpzhdrVfB0g"
          ],
      
          "reactions": "24",
          "comments": "4 comments"
        },
        {
          "Position": "20",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "age": "2mo •",
          "copy": "Laser cutting catheter shafts allows for continuously variable bending stiffness, torsion, and compression to open up a new realm of possibilities for medical device design engineers.\n\nShown below is a 0.027\" OD 0.0025\" wall (0.686mm OD .064mm wall) microcatheter shaft cut on the Medicut Pro Fiber Laser.\n\nContact us at sales@alpinelaser.com for more info.\n\nThank you TRUMPF for the photo.",
          "images": [
            "https://media.licdn.com/dms/image/D5622AQElkuOrteJbWg/feedshare-shrink_800/0/1691003603850?e=1700697600&v=beta&t=zKadIpnMKGNDT1FflkoCtSLu6e_ZCMRSFWNR5U2vPC0"
          ],
      
          "reactions": "30",
          "comments": "0 comments"
        },
        {
          "Position": "21",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "age": "3mo •",
          "copy": "Laser cutting highly reflective material & precious metal alloys can prove challenging for traditional laser workstations.\n\nWe had a recent application inquiry to cut copper and ran the parts on our standard Medicut Pro system equipped with a Fiber Laser. The same success has been demonstrated with precious metal alloys such as Pt, PtIr, PtW, Au, Ag, etc.\n\nPart Description: Copper Tube 2mm OD with a 0.3mm wall\n\nContact us at sales@alpinelaser.com for more info.",
          "images": [
            "https://media.licdn.com/dms/image/D4E22AQH9L9hhXmwLhg/feedshare-shrink_800/0/1690404325098?e=1700697600&v=beta&t=bf4OKRAsom5vyJVJZ1G9oS3Ay3x2Imvr-4EJC2j5Whs"
          ],
          "reactions": "85",
          "comments": "4 comments"
        }
      ]
      EOT;

  // Decode JSON data into a PHP array
  $data = json_decode($json_data, true);

  // Iterate through the array and insert each item into the database
  foreach ($data as $item) {
    $wpdb->insert(
      $table_name,
      array(
        'url' => $item['url'],
        'author' => $item['author'],
        'username' => $item['username'],
        'age' => $item['age'],
        'profilePicture' => $item['profilePicture'],
        'copy' => $item['copy'],
        'images' => json_encode($item['images']),
        'reactions' => $item['reactions'],
        'comments' => $item['comments'],
        'synced' => 0,
        'published' => 0,
        'post_order' => $item['Position']
      ),
      array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d')
    );
  }
}

register_activation_hook(__FILE__, 'linkedin_posts_slider_create_table');
