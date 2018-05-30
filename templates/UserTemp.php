<?php

namespace templates;

use Inc\Base\Secondary;
use Inc\Utils\WPUser;
use Inc\Database\DbBadge;
use Inc\Database\DbUser;
use Inc\Pages\Admin;
use Inc\Utils\Badge;
use Inc\Utils\WPBadge;

/**
 *  Permit to wrap all the function that take care of the user and
 * the badges that he earned
 *
 * All the content to show in the front-end is wrapped in the __() function
 * for internationalization purposes 	
 *
 * @author      @AleRiccardi
 * @since       1.0.0
 *
 * @package     OpenBadgesFramework
 */
final class UserTemp {

    /**
     *  Make start the process only for the back-end.
     */
    public function main() {
        $user = WPUser::getCurrentUser();
        ?>
        <div class="wrap">
            <?php self::getUserPage($user->ID); ?>
        </div>
        <?php

    }

    /**
     * Show all the information about the user.
     *
     *
     *
     * @param int  $idUser  id of the user that we want to show.
     * @param bool $isAdmin understand if we are in the admin area or in the front-end.
     *
     * @return void
     */
    public static function getUserPage($idUser, $isAdmin = true) {
        $userData = get_userdata($idUser);
        $urlImg = esc_url(get_avatar_url($idUser));
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
                                <?php echo $userData->display_name; ?>
                            </li>
                            <li>
                                <span class="dashicons dashicons-calendar"></span>
                                <span> <?php _e('Member since','open-badges-framework'); echo date("d M Y", strtotime($userData->user_registered)); ?></span>
                            </li>
                            <li>
                                <span class="dashicons dashicons-email-alt"></span>
                                <?php echo $userData->user_email; ?>
                            </li>
                            <li>
                                <span class="dashicons dashicons-admin-tools"></span>
                                <?php echo implode(', ', $userData->roles); ?>
                            </li>
                            <li>
                                <?php
                                if ($userData->ID == wp_get_current_user()->ID) {
                                    if (esc_url(get_permalink($rcp_options['edit_profile'])) && Secondary::isRCPActive()) {

                                        ?>
                                        <div class="btn-update-container">
                                            <a href="<?php echo esc_url(get_permalink($rcp_options['edit_profile'])); ?>"
                                               class="btn btn-secondary"><?php _e('Edit your profile','open-badges-framework'); ?></a>
                                        </div>
                                        <?php
                                    } else { ?>
                                        <div class="btn-update-container">
                                            <a href="profile.php"
                                               class="btn btn-secondary"><?php _e('Edit your profile','open-badges-framework'); ?></a>
                                        </div>
                                    <?php }
                                } ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="username-user flex-item">
                    <div class="txt-info center-item">
                        <ul>
                            <li>
                                <span class="dashicons dashicons-info"></span>
                                Year of birth : <?php echo get_the_author_meta( 'year_of_birth', $idUser ); ?>
                            </li>
                            <li>
                                <span class="dashicons dashicons-admin-site"></span>
                                <?php echo get_the_author_meta( 'country', $idUser ); ?> - <?php echo get_the_author_meta( 'city', $idUser ); ?>
                            </li>
                            <li>
                                <span class="dashicons dashicons-translation"></span>
                                <?php echo get_the_author_meta( 'mother_tongue', $idUser ); ?>
                            </li>
                            <li>
                                <span class="dashicons dashicons-welcome-learn-more"></span>
                                <?php 
                                    echo get_the_author_meta( 'primary_degree', $idUser );
                                    if( !empty( get_the_author_meta( 'secondary_degree', $idUser ) ) ){
                                        echo ' - ' . get_the_author_meta( 'secondary_degree', $idUser );
                                    }
                                    if( !empty( get_the_author_meta( 'tertiary_degree', $idUser ) ) ){
                                        echo ' - ' . get_the_author_meta( 'tertiary_degree', $idUser );
                                    }
                                ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <?php
        self::showBadgeEarned($userData->ID, $isAdmin);
    }

    /**
     * Show all the information about the user badges.
     *
     * @param int  $idUser
     * @param bool $isAdmin
     *
     * @return void
     */
    public static function showBadgeEarned($idUser, $isAdmin = true) {
        $userDb = DbUser::getSingle(["idWP" => $idUser]);
		
		if (!$userDb){
			$dbBadges = null;
			
		}else{
			$dbBadges = DbBadge::get(Array("idUser" => $userDb->id));
		}
        
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https' : 'http';
        $toAccept = 0;
        ?>
        <section class="user-badges-cont">
            <div class="user-badges flex-container">
                <div class="title-badges-cont">
                    <h3><?php _e('Badges earned','open-badges-framework'); ?> &nbsp;<span class="dashicons dashicons-yes"></span></h3>
                </div>
                <?php
                if ($dbBadges) {
                    foreach ($dbBadges as $dbBadge) {
                        $badge = new Badge();
                        $badge->retrieveBadge($dbBadge->id);
                        $badgeWP = WPBadge::get($badge->idBadge);
                        if (!$badge->gotDate) $toAccept = 1;
                        if ($badge->gotDate) {

                            ?>
                            <div class="badge flex-item <?php echo !$isAdmin ? "badge-earned" : ""; ?>"
                                 data-id="<?php echo "" . $badge->id; ?>">
                                <a class="wrap-link" <?php
                                if ($isAdmin) {
                                    echo "href='" . admin_url('admin.php?page=' . Admin::PAGE_SINGLE_BADGES, $protocol) . "&badge=" . $badge->id . "&db=1'";
                                } ?>">
                                <div class="cont-img-badge">
                                    <img class="circle-img"
                                         src="<?php echo WPBadge::getUrlImage($badge->idBadge); ?>">
                                </div>
                                <div>
                                    <span><?php echo $badgeWP->post_title; ?></span>
                                </div>
                                </a>
                            </div>
                            <?php
                        }
                    }
                } else {
					?>
                     <p class='lead'><br/>&nbsp;&nbsp;&nbsp;&nbsp; <?php _e('No badges earned','open-badges-framework');?></p>
					 <?php
                }
                ?>
            </div>
            <?php
            if ($toAccept) { ?>
                <div class="obf-badges-to-accept user-badges flex-container">
                    <div class="title-badges-cont">
                        <h4><?php _e('To be accepted','open-badges-framework'); ?></h4>
                    </div>
                    <?php
                    foreach ($dbBadges as $dbBadge) {
                        $badge = new Badge();
                        $badge->retrieveBadge($dbBadge->id);
                        $badgeWP = WPBadge::get($badge->idBadge);
                        if (!$badge->gotDate) {
                            ?>
                            <div class="badge flex-item <?php echo !$isAdmin ? "badge-earned" : ""; ?>"
                                 data-id="<?php echo "" . $badge->id; ?>">
                                <a class="wrap-link" <?php
                                if ($isAdmin) {
                                    echo "href='" . admin_url('admin.php?page=' . Admin::PAGE_SINGLE_BADGES, $protocol) . "&badge=" . $badge->id . "&db=1'";
                                } ?>">
                                <div class="cont-img-badge">
                                    <img class="circle-img"
                                         src="<?php echo WPBadge::getUrlImage($badge->idBadge); ?>">
                                </div>
                                <div>
                                    <span><?php echo $badgeWP->post_title; ?></span>
                                </div>
                                </a>
                            </div>
                            <?php
                        }
                    } ?>
                </div>
                <?php

            }
            ?>

        </section>
        <!-- The Modal -->
        <div id="modalShowBadge" class="modal">
            <!-- Modal content -->
            <div id="responseSent" class="modal-content obf-sbp-conatiner-badge"></div>
        </div>
        <?php
    }
}
