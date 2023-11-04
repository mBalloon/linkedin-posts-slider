<div class="wrap">
    <h1><?php esc_html_e('LinkedIn Posts Management', 'linkedin-posts-slider'); ?></h1>
    <table class="widefat fixed custom-table" cellspacing="0">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-id">ID</th>
                <th scope="col" class="manage-column">Thumbnail</th>
                <th scope="col" class="manage-column">Age</th>
                <th scope="col" class="manage-column">Post Text</th>
                <th scope="col" class="manage-column">Actions</th>
                <th scope="col" class="manage-column">Order</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row) : ?>
                <tr id="post-<?php echo esc_attr($row->id); ?>">
                    <td class="row-id" id="row-id"><?php echo esc_html($row->id); ?></td>
                    <td class="thumbnail-cell">
                        <?php
                        $images = json_decode($row->images);
                        if (!empty($images)) {
                            echo '<img src="' . esc_url($images[0]) . '" alt="" width="100" height="100" />';  /* Adjust width and height as needed */
                        }
                        ?>
                    </td>
                    <td><?php echo esc_html($row->age); ?></td>
                    <td><?php echo wp_trim_words(esc_html($row->post_text), 10, '...'); ?></td>
                    <td>
                        <div class="action-buttons">
                            <form method="post" style="display:inline;">
                                <?php //wp_nonce_field('linkedin_delete_action', 'linkedin_delete_nonce'); 
                                ?>
                                <input type="hidden" name="id" value="<?php echo esc_attr($row->id); ?>">
                                <input type="submit" value="Delete" class="delete-button" data-id="<?php echo esc_attr($row->id); ?>">
                            </form>

                            <button class="publish-button" data-id="<?php echo esc_attr($row->id); ?>" data-published="<?php echo esc_attr($row->published); ?>">
                                <?php echo $row->published ? 'Published' : 'Unpublished'; ?>
                            </button>
                        </div>


                    </td>
                    </td>
                    <td>
                        <div class="up-down-wrapper">
                            <button class="up-button">
                                <!-- Up arrow SVG -->
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 5L5 12H19L12 5Z" fill="white" />
                                </svg>
                            </button>
                            <button class="down-button">
                                <!-- Down arrow SVG -->
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 19L5 12H19L12 19Z" fill="white" />
                                </svg>
                            </button>
                        </div>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>