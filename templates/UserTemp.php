<?php

namespace templates;

use Inc\Base\Secondary;
use Inc\Base\User;
use Inc\Database\DbBadge;
use Inc\Pages\Admin;
use Inc\Utils\Badges;

/**
 *  Permit to wrap all the function that take care of the user and
 * the badges that he earned
 *
 * @author      Alessandro RICCARDI
 * @since       1.0.0
 *
 * @package     OpenBadgesFramework
 */
final class UserTemp {

    /**
     *  Make start the process but only for the back-end.
     */
    public function main() {
        $user = User::getCurrentUser();
        ?>
        <div class="wrap">
            <?php self::getUserPage($user->ID); ?>
        </div>
        <?php

    }

    /**
     *  Show all the information and the badges about the user.
     *
     * @param int  $idUser id of the user that we want to show
     * @param bool $isAdmin understand if we are in the admin areao or in the front-end
     */
    public static function getUserPage($idUser, $isAdmin = true) {
        $userData = get_userdata($idUser);
        $urlImg = esc_url(get_avatar_url($idUser));
        $dbBadges = DbBadge::get(Array("UserEmail" => $userData->user_email));
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https' : 'http';

        $rcp_options = get_option('rcp_settings');
        ?>

        <div class="author-name">
            <h1 class="">
                <?php echo $userData->first_name; ?>&nbsp;<?php echo $userData->last_name; ?>
            </h1>
        </div>
        <section>
            <div class="user-info-admin flex-container">
                <div class="img-user flex-item">
                    <img class="circle-img" src="<?php echo $urlImg; ?>">
                </div>

                <div class="username-user center-container flex-item">
                    <div class="txt-info center-item">
                        <ul>
                            <li>
                                <span class="dashicons dashicons-admin-users"></span>
                                <?php echo $userData->nickname; ?>
                            </li>
                            <li>
                                <span class="dashicons dashicons-calendar"></span>
                                <span>Member since <?php echo date("d M Y", strtotime($userData->user_registered)); ?></span>
                            </li>
                            <li>
                                <span class="dashicons dashicons-email-alt"></span>
                                <?php echo $userData->user_email; ?>
                            </li>
                            <li>
                                <span class="dashicons dashicons-admin-tools"></span>
                                <?php echo get_user_meta(get_queried_object_id(), 'rcp_profession', true); ?>
                            </li>
                            <li>
                                <?php
                                if ($userData->ID == wp_get_current_user()->ID) {
                                    if (esc_url(get_permalink($rcp_options['edit_profile']))) {

                                        ?>
                                        <div class="btn-update-container">
                                            <a href="<?php echo esc_url(get_permalink($rcp_options['edit_profile'])); ?>"
                                               class="btn btn-secondary">Edit your profile</a>
                                        </div>
                                        <?php
                                    } else { ?>
                                        <div class="btn-update-container">
                                            <a href="profile.php"
                                               class="btn btn-secondary">Edit your profile</a>
                                        </div>
                                    <?php }
                                } ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <section class="user-badges-cont">
            <div class="title-badges-cont">
                <h3>Badges earned</h3>
            </div>
            <div class="user-badges flex-container">
                <?php
                if ($dbBadges) {
                    foreach ($dbBadges as $dbBadge) { ?>
                        <div class="badge flex-item <?php echo !$isAdmin ? "badge-earned" : ""; ?>" name="<?php echo "".$dbBadge->id; ?>">
                            <a class="wrap-link" <?php
                               if ($isAdmin) {
                                   echo "href='" . admin_url('admin.php?page=' . Admin::PAGE_SINGLE_BADGES, $protocol) . "&badge=$dbBadge->id&db=1'";
                               } ?>">
                                <div class="cont-img-badge">
                                    <img class="circle-img" src="<?php echo Badges::getImage($dbBadge->badgeId); ?>">
                                </div>
                                <div>
                                    <span><?php echo Badges::get($dbBadge->badgeId)->post_title; ?></span>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='lead'>No badge earned</p>";
                }
                ?>
            </div>
        </section>
        <!-- The Modal -->
        <div id="modalShowBadge" class="modal">
            <!-- Modal content -->
            <div id="responseSent" class="modal-content obf-sbp-conatiner-badge"></div>
        </div>
        <?php
    }
}
