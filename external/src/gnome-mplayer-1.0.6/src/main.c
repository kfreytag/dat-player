/* -*- Mode: C; indent-tabs-mode: t; c-basic-offset: 4; tab-width: 4 -*- */
/*
 * main.c
 * Copyright (C) Kevin DeKorte 2006 <kdekorte@gmail.com>
 *
 * main.c is free software.
 *
 * You may redistribute it and/or modify it under the terms of the
 * GNU General Public License, as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * main.c is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with main.c.  If not, write to:
 * 	The Free Software Foundation, Inc.,
 * 	51 Franklin Street, Fifth Floor
 * 	Boston, MA  02110-1301, USA.
 */

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <sys/types.h>
#include <sys/stat.h>
#include <mntent_compat.h>
#include <unistd.h>
#include <string.h>
#include <stdio.h>
#include <libintl.h>
#include <signal.h>

//#include <bonobo.h>
// #include <gnome.h>
#include <glib/gstdio.h>
#include <glib/gi18n.h>

#include "common.h"
#include "support.h"
#include "dbus-interface.h"
#include "gmtk.h"


static GOptionEntry entries[] = {
    {"window", 0, 0, G_OPTION_ARG_INT, &embed_window, N_("Window to embed in"), "WID"},
    {"width", 'w', 0, G_OPTION_ARG_INT, &window_x, N_("Width of window to embed in"), "X"},
    {"height", 'h', 0, G_OPTION_ARG_INT, &window_y, N_("Height of window to embed in"), "Y"},
    {"controlid", 0, 0, G_OPTION_ARG_INT, &control_id, N_("Unique DBUS controller id"), "CID"},
    {"playlist", 0, 0, G_OPTION_ARG_NONE, &playlist, N_("File Argument is a playlist"), NULL},
    {"verbose", 'v', 0, G_OPTION_ARG_NONE, &verbose, N_("Show more output on the console"), NULL},
    {"reallyverbose", '\0', 0, G_OPTION_ARG_NONE, &reallyverbose,
     N_("Show even more output on the console"), NULL},
    {"fullscreen", 0, 0, G_OPTION_ARG_NONE, &init_fullscreen, N_("Start in fullscreen mode"), NULL},
    {"softvol", 0, 0, G_OPTION_ARG_NONE, &softvol, N_("Use mplayer software volume control"), NULL},
    {"remember_softvol", 0, 0, G_OPTION_ARG_NONE, &remember_softvol,
     N_("When set to TRUE the last volume level is set as the default"), NULL},
    {"volume_softvol", 0, 0, G_OPTION_ARG_INT, &volume_softvol,
     N_("Last software volume percentage- only applied when remember_softvol is set to TRUE"),
     NULL},
    {"mixer", 0, 0, G_OPTION_ARG_STRING, &(audio_device.alsa_mixer), N_("Mixer to use"), NULL},
    {"volume", 0, 0, G_OPTION_ARG_INT, &(pref_volume), N_("Set initial volume percentage"), NULL},
    {"showcontrols", 0, 0, G_OPTION_ARG_INT, &showcontrols, N_("Show the controls in window"),
     "[0|1]"},
    {"showsubtitles", 0, 0, G_OPTION_ARG_INT, &showsubtitles, N_("Show the subtitles if available"),
     "[0|1]"},
    {"autostart", 0, 0, G_OPTION_ARG_INT, &autostart,
     N_("Autostart the media default to 1, set to 0 to load but don't play"), "[0|1]"},
    {"disablecontextmenu", 0, 0, G_OPTION_ARG_NONE, &disable_context_menu,
     N_("Disable popup menu on right click"), NULL},
    {"disablefullscreen", 0, 0, G_OPTION_ARG_NONE, &disable_fullscreen,
     N_("Disable fullscreen options in browser mode"), NULL},
    {"loop", 0, 0, G_OPTION_ARG_NONE, &loop, N_("Play all files on the playlist forever"), NULL},
    {"quit_on_complete", 'q', 0, G_OPTION_ARG_NONE, &quit_on_complete,
     N_("Quit application when last file on playlist is played"), NULL},
    {"random", 0, 0, G_OPTION_ARG_NONE, &random_order, N_("Play items on playlist in random order"),
     NULL},
    {"cache", 0, 0, G_OPTION_ARG_INT, &cache_size, N_("Set cache size"),
     NULL},
    {"ss", 0, 0, G_OPTION_ARG_INT, &start_second, N_("Start Second (default to 0)"),
     NULL},
    {"endpos", 0, 0, G_OPTION_ARG_INT, &play_length,
     N_("Length of media to play from start second"),
     NULL},
    {"forcecache", 0, 0, G_OPTION_ARG_NONE, &forcecache, N_("Force cache usage on streaming sites"),
     NULL},
    {"disabledeinterlace", 0, 0, G_OPTION_ARG_NONE, &disable_deinterlace,
     N_("Disable the deinterlace filter"),
     NULL},
    {"disableframedrop", 0, 0, G_OPTION_ARG_NONE, &disable_framedrop,
     N_("Don't skip drawing frames to better keep sync"),
     NULL},
    {"disableass", 0, 0, G_OPTION_ARG_NONE, &disable_ass,
     N_("Use the old subtitle rendering system"),
     NULL},
    {"disableembeddedfonts", 0, 0, G_OPTION_ARG_NONE, &disable_embeddedfonts,
     N_("Don't use fonts embedded on matroska files"),
     NULL},
    {"vertical", 0, 0, G_OPTION_ARG_NONE, &vertical_layout, N_("Use Vertical Layout"),
     NULL},
    {"showplaylist", 0, 0, G_OPTION_ARG_NONE, &playlist_visible, N_("Start with playlist open"),
     NULL},
    {"showdetails", 0, 0, G_OPTION_ARG_NONE, &details_visible, N_("Start with details visible"),
     NULL},
    {"rpname", 0, 0, G_OPTION_ARG_STRING, &rpname, N_("Real Player Name"), "NAME"},
    {"rpconsole", 0, 0, G_OPTION_ARG_STRING, &rpconsole, N_("Real Player Console ID"), "CONSOLE"},
    {"rpcontrols", 0, 0, G_OPTION_ARG_STRING, &rpcontrols, N_("Real Player Console Controls"),
     "Control Name,..."},
    {"subtitle", 0, 0, G_OPTION_ARG_STRING, &subtitle, N_("Subtitle file for first media file"),
     "FILENAME"},
    {"tvdevice", 0, 0, G_OPTION_ARG_STRING, &tv_device, N_("TV device name"), "DEVICE"},
    {"tvdriver", 0, 0, G_OPTION_ARG_STRING, &tv_driver, N_("TV driver name (v4l|v4l2)"), "DRIVER"},
    {"tvinput", 0, 0, G_OPTION_ARG_STRING, &tv_input, N_("TV input name"), "INPUT"},
    {"tvwidth", 0, 0, G_OPTION_ARG_INT, &tv_width, N_("Width of TV input"), "WIDTH"},
    {"tvheight", 0, 0, G_OPTION_ARG_INT, &tv_height, N_("Height of TV input"), "HEIGHT"},
    {"tvfps", 0, 0, G_OPTION_ARG_INT, &tv_fps, N_("Frames per second from TV input"), "FPS"},
    {"single_instance", 0, 0, G_OPTION_ARG_NONE, &single_instance, N_("Only allow one instance"),
     NULL},
    {"replace_and_play", 0, 0, G_OPTION_ARG_NONE, &replace_and_play,
     N_("Put single instance mode into replace and play mode"),
     NULL},
    {"large_buttons", 0, 0, G_OPTION_ARG_NONE, &large_buttons,
     N_("Use large control buttons"),
     NULL},
    {"always_hide_after_timeout", 0, 0, G_OPTION_ARG_NONE, &always_hide_after_timeout,
     N_("Hide control panel when mouse is not moving"),
     NULL},
    {"new_instance", 0, 0, G_OPTION_ARG_NONE, &new_instance,
     N_("Ignore single instance preference for this instance"),
     NULL},
    {"keep_on_top", 0, 0, G_OPTION_ARG_NONE, &keep_on_top,
     N_("Keep window on top"),
     NULL},
#ifdef HAVE_GPOD
    {"load_tracks_from_gpod", 0, 0, G_OPTION_ARG_NONE, &load_tracks_from_gpod,
     N_("Load all tracks from media player using gpod"),
     NULL},
#endif
    {"disable_cover_art_fetch", 0, 0, G_OPTION_ARG_NONE, &disable_cover_art_fetch,
     N_("Don't fetch new cover art images"),
     NULL},
    {"dvd_device", 0, 0, G_OPTION_ARG_STRING, &option_dvd_device, N_("DVD Device Name"), "Path to device or folder"},
    {"vo", 0, 0, G_OPTION_ARG_STRING, &option_vo, N_("Video Output Device Name"), "mplayer vo name"},
    {NULL}
};

gboolean async_play_iter(void *data)
{
    next_iter = (GtkTreeIter *) (data);
    // printf("state = %i\n", gmtk_media_player_get_state(GMTK_MEDIA_PLAYER(media)));
    if (gmtk_media_player_get_state(GMTK_MEDIA_PLAYER(media)) == MEDIA_STATE_UNKNOWN) {
        play_iter(next_iter, 0);
        next_iter = NULL;
    }

    return FALSE;
}

gboolean play(void *data)
{
    PlayData *p = (PlayData *) data;

    if (ok_to_play && p != NULL) {
        if (!gtk_list_store_iter_is_valid(playliststore, &iter)) {
            // printf("iter is not valid, getting first one\n");
            gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter);
        }
        gtk_list_store_set(playliststore, &iter, PLAYLIST_COLUMN, p->playlist, ITEM_COLUMN, p->uri, -1);
        play_iter(&iter, 0);
    }
    g_free(p);

    return FALSE;
}


void play_next()
{
    gchar *filename;
    gint count;
    PlayData *p = NULL;

    if (next_item_in_playlist(&iter)) {
        if (gtk_list_store_iter_is_valid(playliststore, &iter)) {
            gtk_tree_model_get(GTK_TREE_MODEL(playliststore), &iter, ITEM_COLUMN, &filename,
                               COUNT_COLUMN, &count, PLAYLIST_COLUMN, &playlist, -1);
            g_strlcpy(idledata->info, filename, 4096);
            g_idle_add(set_media_info, idledata);
            p = (PlayData *) g_malloc(sizeof(PlayData));
            g_strlcpy(p->uri, filename, 4096);
            p->playlist = playlist;
            g_idle_add(play, p);
            g_free(filename);
        }
    } else {
        // printf("end of thread playlist is empty\n");
        if (loop) {
            if (first_item_in_playlist(&iter)) {
                gtk_tree_model_get(GTK_TREE_MODEL(playliststore), &iter, ITEM_COLUMN,
                                   &filename, COUNT_COLUMN, &count, PLAYLIST_COLUMN, &playlist, -1);
                g_strlcpy(idledata->info, filename, 4096);
                g_idle_add(set_media_info, idledata);
                p = (PlayData *) g_malloc(sizeof(PlayData));
                g_strlcpy(p->uri, filename, 4096);
                p->playlist = playlist;
                g_idle_add(play, p);
                g_free(filename);
            }
        } else {
            idledata->fullscreen = 0;
            g_idle_add(set_fullscreen, idledata);
            g_idle_add(set_stop, idledata);
        }

        if (quit_on_complete) {
            g_idle_add(set_quit, idledata);
        }
    }
}


gint play_iter(GtkTreeIter * playiter, gint restart_second)
{

    gchar *subtitle = NULL;
    gchar *audiofile = NULL;
    GtkTreePath *path;
    gchar *uri = NULL;
    gint count;
    gint playlist;
    gchar *title = NULL;
    gchar *artist = NULL;
    gchar *album = NULL;
    gchar *audio_codec;
    gchar *video_codec = NULL;
    GtkAllocation alloc;
    gchar *demuxer = NULL;
    gboolean playable = TRUE;
    gint width;
    gint height;
    gfloat length_value;
    gint i;
    gpointer pixbuf;
    gchar *buffer = NULL;
    gchar *message = NULL;
    MetaData *metadata;
#ifdef GTK2_12_ENABLED
    GtkRecentData *recent_data;
#ifdef GIO_ENABLED
    GFile *file;
    GFileInfo *file_info;
#endif
#endif

    /*
       if (!(gmtk_media_player_get_state(GMTK_MEDIA_PLAYER(media)) == MEDIA_STATE_UNKNOWN ||
       gmtk_media_player_get_state(GMTK_MEDIA_PLAYER(media)) == MEDIA_STATE_QUIT)) {
       while (gmtk_media_player_get_state(GMTK_MEDIA_PLAYER(media)) != MEDIA_STATE_UNKNOWN) {
       gtk_main_iteration();
       }
       }
     */

    if (gtk_list_store_iter_is_valid(playliststore, playiter)) {
        gtk_tree_model_get(GTK_TREE_MODEL(playliststore), playiter, ITEM_COLUMN, &uri,
                           DESCRIPTION_COLUMN, &title, LENGTH_VALUE_COLUMN, &length_value,
                           ARTIST_COLUMN, &artist,
                           ALBUM_COLUMN, &album,
                           AUDIO_CODEC_COLUMN, &audio_codec,
                           VIDEO_CODEC_COLUMN, &video_codec,
                           VIDEO_WIDTH_COLUMN, &width,
                           VIDEO_HEIGHT_COLUMN, &height,
                           DEMUXER_COLUMN, &demuxer,
                           COVERART_COLUMN, &pixbuf,
                           SUBTITLE_COLUMN, &subtitle,
                           AUDIOFILE_COLUMN, &audiofile,
                           COUNT_COLUMN, &count, PLAYLIST_COLUMN, &playlist, PLAYABLE_COLUMN, &playable, -1);
        if (GTK_IS_TREE_SELECTION(selection)) {
            path = gtk_tree_model_get_path(GTK_TREE_MODEL(playliststore), playiter);
            if (path) {
                gtk_tree_selection_select_path(selection, path);
                if (GTK_IS_WIDGET(list))
                    gtk_tree_view_scroll_to_cell(GTK_TREE_VIEW(list), path, NULL, FALSE, 0, 0);
                buffer = gtk_tree_path_to_string(path);
                g_free(buffer);
                gtk_tree_path_free(path);
            }
        }
        gtk_list_store_set(playliststore, playiter, COUNT_COLUMN, count + 1, -1);
    } else {
        if (verbose > 1)
            printf("iter is invalid, nothing to play\n");
        return 0;
    }

    if (verbose) {
        printf("playing - %s\n", uri);
        printf("is playlist %i\n", playlist);
    }

    gmtk_get_allocation(GTK_WIDGET(media), &alloc);
    if (width == 0 || height == 0) {
        alloc.width = 16;
        alloc.height = 16;
    } else {
        alloc.width = width;
        alloc.height = height;
    }
    //printf("setting window size to %i x %i\n", alloc.width, alloc.height);
    gtk_widget_size_allocate(GTK_WIDGET(media), &alloc);
    while (gtk_events_pending())
        gtk_main_iteration();

    /*
       // wait for metadata to be available on this item
       if (!streaming_media(uri) && !device_name(uri)) {
       i = 0;
       if (playable) {
       while (demuxer == NULL && i < 50) {
       g_free(title);
       g_free(artist);
       g_free(album);
       g_free(audio_codec);
       g_free(video_codec);
       g_free(demuxer);
       g_free(subtitle);
       g_free(audiofile);
       if (gtk_list_store_iter_is_valid(playliststore, playiter)) {
       gtk_tree_model_get(GTK_TREE_MODEL(playliststore), playiter, LENGTH_VALUE_COLUMN,
       &length_value, DESCRIPTION_COLUMN, &title, ARTIST_COLUMN,
       &artist, ALBUM_COLUMN, &album, AUDIO_CODEC_COLUMN,
       &audio_codec, VIDEO_CODEC_COLUMN, &video_codec,
       VIDEO_WIDTH_COLUMN, &width, VIDEO_HEIGHT_COLUMN, &height,
       DEMUXER_COLUMN, &demuxer, COVERART_COLUMN, &pixbuf,
       SUBTITLE_COLUMN, &subtitle, AUDIOFILE_COLUMN, &audiofile,
       COUNT_COLUMN, &count, PLAYLIST_COLUMN, &playlist,
       PLAYABLE_COLUMN, &playable, -1);
       if (!playable) {
       if (verbose)
       printf("%s is not marked as playable (%i)\n", uri, i);
       play_next();
       return 0;
       }
       } else {
       if (verbose)
       printf("Current iter is not valid\n");
       return 1;   // error condition
       }
       gtk_main_iteration();
       i++;
       if (demuxer == NULL)
       g_usleep(10000);
       }
       } else {
       if (verbose)
       printf("%s is not marked as playable\n", uri);
       play_next();
       return 0;
       }

       }
     */
    // reset audio meter
    for (i = 0; i < METER_BARS; i++) {
        buckets[i] = 0;
        max_buckets[i] = 0;
    }

    gmtk_media_tracker_set_text(tracker, _("Playing"));
    gmtk_media_tracker_set_position(tracker, (gfloat) restart_second);
    gmtk_media_tracker_set_length(tracker, length_value);

    message = g_strdup_printf("<small>\n");
    if (title == NULL) {
        title = g_filename_display_basename(uri);
    }
    buffer = g_markup_printf_escaped("\t<big><b>%s</b></big>\n", title);
    message = g_strconcat(message, buffer, NULL);
    g_free(buffer);

    if (artist != NULL) {
        buffer = g_markup_printf_escaped("\t<i>%s</i>\n", artist);
        message = g_strconcat(message, buffer, NULL);
        g_free(buffer);
    }
    if (album != NULL) {
        buffer = g_markup_printf_escaped("\t%s\n", album);
        message = g_strconcat(message, buffer, NULL);
        g_free(buffer);
    }
    //buffer = g_markup_printf_escaped("\n\t%s\n", uri);
    //message = g_strconcat(message, buffer, NULL);
    //g_free(buffer);

    message = g_strconcat(message, "</small>", NULL);

    // probably not much cover art for random video files
    if (pixbuf == NULL && video_codec == NULL && !streaming_media(uri) && control_id == 0 && !playlist) {
        metadata = (MetaData *) g_new0(MetaData, 1);
        metadata->uri = g_strdup(uri);
        if (title != NULL)
            metadata->title = g_strstrip(g_strdup(title));
        if (artist != NULL)
            metadata->artist = g_strstrip(g_strdup(artist));
        if (album != NULL)
            metadata->album = g_strstrip(g_strdup(album));
        g_thread_create(get_cover_art, metadata, FALSE, NULL);
    } else {
        gtk_image_clear(GTK_IMAGE(cover_art));
    }

    g_strlcpy(idledata->media_info, message, 1024);
    g_strlcpy(idledata->display_name, title, 1024);
    g_free(message);

    message = gm_tempname(NULL, "mplayer-af_exportXXXXXX");
    g_strlcpy(idledata->af_export, message, 1024);
    g_free(message);

    message = g_strdup("");
    if (title == NULL) {
        title = g_filename_display_basename(uri);
    }
    //buffer = g_markup_printf_escaped("\t<b>%s</b>\n", title);
    //message = g_strconcat(message, buffer, NULL);
    //g_free(buffer);

    if (artist != NULL) {
        buffer = g_markup_printf_escaped("\t<i>%s</i>\n", artist);
        message = g_strconcat(message, buffer, NULL);
        g_free(buffer);
    }
    if (album != NULL) {
        buffer = g_markup_printf_escaped("\t%s\n", album);
        message = g_strconcat(message, buffer, NULL);
        g_free(buffer);
    }
    g_strlcpy(idledata->media_notification, message, 1024);
    g_free(message);

    if (control_id == 0) {
        set_media_label(idledata);
    } else {
        gtk_check_menu_item_set_active(GTK_CHECK_MENU_ITEM(menuitem_view_info), FALSE);
    }

    if (subtitles)
        gtk_container_forall(GTK_CONTAINER(subtitles), remove_langs, NULL);
    gtk_widget_set_sensitive(GTK_WIDGET(menuitem_edit_select_sub_lang), FALSE);
    if (tracks)
        gtk_container_forall(GTK_CONTAINER(tracks), remove_langs, NULL);
    gtk_widget_set_sensitive(GTK_WIDGET(menuitem_edit_select_audio_lang), FALSE);
    lang_group = NULL;
    audio_group = NULL;


    if (subtitle != NULL) {
        gmtk_media_player_set_attribute_string(GMTK_MEDIA_PLAYER(media), ATTRIBUTE_SUBTITLE_FILE, subtitle);
        g_free(subtitle);
        subtitle = NULL;
    }
    if (audiofile != NULL) {
        gmtk_media_player_set_attribute_string(GMTK_MEDIA_PLAYER(media), ATTRIBUTE_AUDIO_TRACK_FILE, audiofile);
        g_free(audiofile);
        audiofile = NULL;
    }

    /*
       if (g_ascii_strcasecmp(thread_data->filename, "") != 0) {
       if (!device_name(thread_data->filename) && !streaming_media(thread_data->filename)) {
       if (!g_file_test(thread_data->filename, G_FILE_TEST_EXISTS)) {
       error_msg = g_strdup_printf("%s not found\n", thread_data->filename);
       dialog =
       gtk_message_dialog_new(NULL, GTK_DIALOG_DESTROY_WITH_PARENT,
       GTK_MESSAGE_ERROR, GTK_BUTTONS_CLOSE, "%s", error_msg);
       gtk_window_set_title(GTK_WINDOW(dialog), "GNOME MPlayer Error");
       gtk_dialog_run(GTK_DIALOG(dialog));
       gtk_widget_destroy(dialog);
       return 1;
       }
       }
       }
     */
#ifdef GTK2_12_ENABLED
#ifdef GIO_ENABLED
    // don't put it on the recent list, if it is running in plugin mode
    if (control_id == 0 && !streaming_media(uri)) {
        recent_data = (GtkRecentData *) g_new0(GtkRecentData, 1);
        if (artist != NULL && strlen(artist) > 0) {
            recent_data->display_name = g_strdup_printf("%s - %s", artist, title);
        } else {
            recent_data->display_name = g_strdup(title);
        }
        g_strlcpy(idledata->display_name, recent_data->display_name, 1024);


        file = g_file_new_for_uri(uri);
        file_info = g_file_query_info(file,
                                      G_FILE_ATTRIBUTE_STANDARD_CONTENT_TYPE ","
                                      G_FILE_ATTRIBUTE_STANDARD_DISPLAY_NAME, G_FILE_QUERY_INFO_NONE, NULL, NULL);


        if (file_info) {
            recent_data->mime_type = g_strdup(g_file_info_get_content_type(file_info));
            g_object_unref(file_info);
        }
        g_object_unref(file);
        recent_data->app_name = g_strdup("gnome-mplayer");
        recent_data->app_exec = g_strdup("gnome-mplayer %u");
        if (recent_data->mime_type != NULL) {
            gtk_recent_manager_add_full(recent_manager, uri, recent_data);
            g_free(recent_data->mime_type);
        }
        g_free(recent_data->app_name);
        g_free(recent_data->app_exec);
        g_free(recent_data);

    }
#endif
#endif
    g_free(title);
    g_free(artist);
    g_free(album);
    if (demuxer != NULL) {
        g_strlcpy(idledata->demuxer, demuxer, 64);
        g_free(demuxer);
    } else {
        g_strlcpy(idledata->demuxer, "", 64);
    }

    last_x = 0;
    last_y = 0;
    idledata->width = width;
    idledata->height = height;

    idledata->retry_on_full_cache = FALSE;
    idledata->cachepercent = -1.0;
    g_strlcpy(idledata->info, uri, 1024);
    set_media_info(idledata);

    streaming = 0;

    gm_store = gm_pref_store_new("gnome-mplayer");
    forcecache = gm_pref_store_get_boolean(gm_store, FORCECACHE);
    gm_pref_store_free(gm_store);


    if (g_ascii_strcasecmp(uri, "dvdnav://") == 0) {
        gtk_widget_show(menu_event_box);
    } else {
        gtk_widget_hide(menu_event_box);
    }

    if (autostart) {
        g_idle_add(hide_buttons, idledata);
        js_state = STATE_PLAYING;

        if (g_str_has_prefix(uri, "mmshttp") || g_str_has_prefix(uri, "http") || g_str_has_prefix(uri, "mms")) {
            gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_NETWORK);
        } else if (g_str_has_prefix(uri, "dvd") || g_str_has_prefix(uri, "dvdnav")) {
            gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_DVD);
        } else if (g_str_has_prefix(uri, "cdda")) {
            gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_CD);
        } else if (g_str_has_prefix(uri, "cddb")) {
            gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_CD);
        } else if (g_str_has_prefix(uri, "vcd")) {
            gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_VCD);
        } else if (g_str_has_prefix(uri, "tv")) {
            gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_TV);
        } else if (g_str_has_prefix(uri, "dvb")) {
            gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_DVB);
        } else if (g_str_has_prefix(uri, "file")) {
            gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_FILE);
        } else {
            // if all else fails it must be a network type
            gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_NETWORK);
        }

        gmtk_media_player_set_attribute_boolean(GMTK_MEDIA_PLAYER(media), ATTRIBUTE_PLAYLIST, playlist);
        gmtk_media_player_set_uri(GMTK_MEDIA_PLAYER(media), uri);
        gmtk_media_player_set_state(GMTK_MEDIA_PLAYER(media), MEDIA_STATE_PLAY);

    }

    return 0;
}

#ifndef OS_WIN32
static void hup_handler(int signum)
{
    // printf("handling signal %i\n",signum);
    delete_callback(NULL, NULL, NULL);
    g_idle_add(set_destroy, NULL);
}
#endif

void assign_default_keys()
{
    if (!accel_keys[FILE_OPEN_LOCATION]) {
        accel_keys[FILE_OPEN_LOCATION] = g_strdup("4+l");
    }
    if (!accel_keys[EDIT_SCREENSHOT]) {
        accel_keys[EDIT_SCREENSHOT] = g_strdup("4+t");
    }
    if (!accel_keys[EDIT_PREFERENCES]) {
        accel_keys[EDIT_PREFERENCES] = g_strdup("4+p");
    }
    if (!accel_keys[VIEW_PLAYLIST]) {
        accel_keys[VIEW_PLAYLIST] = g_strdup("F9");
    }
    if (!accel_keys[VIEW_INFO]) {
        accel_keys[VIEW_INFO] = g_strdup("i");
    }
    if (!accel_keys[VIEW_DETAILS]) {
        accel_keys[VIEW_DETAILS] = g_strdup("4+d");
    }
    if (!accel_keys[VIEW_METER]) {
        accel_keys[VIEW_METER] = g_strdup("4+m");
    }
    if (!accel_keys[VIEW_FULLSCREEN]) {
        accel_keys[VIEW_FULLSCREEN] = g_strdup("4+f");
    }
    if (!accel_keys[VIEW_ASPECT]) {
        accel_keys[VIEW_ASPECT] = g_strdup("a");
    }
    if (!accel_keys[VIEW_SUBTITLES]) {
        accel_keys[VIEW_SUBTITLES] = g_strdup("v");
    }
    if (!accel_keys[VIEW_DECREASE_SIZE]) {
        accel_keys[VIEW_DECREASE_SIZE] = g_strdup("1+R");
    }
    if (!accel_keys[VIEW_INCREASE_SIZE]) {
        accel_keys[VIEW_INCREASE_SIZE] = g_strdup("1+T");
    }
    if (!accel_keys[VIEW_ANGLE]) {
        accel_keys[VIEW_ANGLE] = g_strdup("4+a");
    }
    if (!accel_keys[VIEW_CONTROLS]) {
        accel_keys[VIEW_CONTROLS] = g_strdup("c");
    }
}

int main(int argc, char *argv[])
{
    struct stat buf = { 0 };
    struct mntent *mnt = NULL;
    FILE *fp = NULL;
    gchar *uri;
    gint fileindex = 1;
    GError *error = NULL;
    GOptionContext *context;
    gint i;
    gdouble volume = 100.0;
    gchar *accelerator_keys;
    gchar **parse;
#ifdef GTK3_ENABLED
    GtkSettings *gtk_settings;
#endif
    int stat_result;

#ifndef OS_WIN32
    struct sigaction sa;
#endif
    gboolean playiter = FALSE;

#ifdef GIO_ENABLED
    GFile *file;
#endif

#ifdef ENABLE_NLS
    bindtextdomain(GETTEXT_PACKAGE, PACKAGE_LOCALE_DIR);
    bind_textdomain_codeset(GETTEXT_PACKAGE, "UTF-8");
    textdomain(GETTEXT_PACKAGE);
#endif

    playlist = 0;
    embed_window = 0;
    control_id = 0;
    window_x = 0;
    window_y = 0;
    last_window_width = 0;
    last_window_height = 0;
    showcontrols = 1;
    showsubtitles = TRUE;
    autostart = 1;
    videopresent = 0;
    disable_context_menu = FALSE;
    dontplaynext = FALSE;
    idledata = (IdleData *) g_new0(IdleData, 1);
    idledata->videopresent = FALSE;
    idledata->length = 0.0;
    idledata->device = NULL;
    idledata->cachepercent = -1.0;
    selection = NULL;
    path = NULL;
    js_state = STATE_UNDEFINED;
    control_instance = TRUE;
    playlistname = NULL;
    rpconsole = NULL;
    subtitle = NULL;
    tv_device = NULL;
    tv_driver = NULL;
    tv_width = 0;
    tv_height = 0;
    tv_fps = 0;
    ok_to_play = TRUE;
    alang = NULL;
    slang = NULL;
    metadata_codepage = NULL;
    playlistname = NULL;
    window_width = -1;
    window_height = -1;
    stored_window_width = -1;
    stored_window_height = -1;
    cache_size = 0;
    forcecache = FALSE;
    use_volume_option = FALSE;
    vertical_layout = FALSE;
    playlist_visible = FALSE;
    disable_fullscreen = FALSE;
    disable_framedrop = FALSE;
    softvol = FALSE;
    remember_softvol = FALSE;
    volume_softvol = -1;
    volume_gain = 0;
    subtitlefont = NULL;
    subtitle_codepage = NULL;
    subtitle_color = NULL;
    subtitle_outline = FALSE;
    subtitle_shadow = FALSE;
    subtitle_fuzziness = 0;
    disable_embeddedfonts = FALSE;
    quit_on_complete = FALSE;
    verbose = 0;
    reallyverbose = 0;
    embedding_disabled = FALSE;
    disable_pause_on_click = FALSE;
    disable_animation = FALSE;
    auto_hide_timeout = 3;
    mouse_over_controls = FALSE;
    use_mediakeys = TRUE;
    use_defaultpl = FALSE;
    mplayer_bin = NULL;
    mplayer_dvd_device = NULL;
    single_instance = FALSE;
    disable_deinterlace = TRUE;
    details_visible = FALSE;
    replace_and_play = FALSE;
    bring_to_front = FALSE;
    keep_on_top = FALSE;
    resize_on_new_media = FALSE;
    use_pausing_keep_force = FALSE;
    show_notification = TRUE;
    show_status_icon = TRUE;
    lang_group = NULL;
    audio_group = NULL;
    gpod_mount_point = NULL;
    load_tracks_from_gpod = FALSE;
    disable_cover_art_fetch = FALSE;
    fullscreen = 0;
    vo = NULL;
    data = NULL;
    max_data = NULL;
    details_table = NULL;
    large_buttons = FALSE;
    button_size = GTK_ICON_SIZE_BUTTON;
    lastguistate = -1;
    non_fs_height = 0;
    non_fs_width = 0;
    use_hw_audio = FALSE;
    start_second = 0;
    play_length = 0;
    save_loc = TRUE;
    use_xscrnsaver = FALSE;
    screensaver_disabled = FALSE;
    update_control_flag = FALSE;
    gchar *filename;
    skip_fixed_allocation_on_show = FALSE;
    skip_fixed_allocation_on_hide = FALSE;
    pref_volume = -1;
    use_mplayer2 = FALSE;
    enable_global_menu = FALSE;

#ifndef OS_WIN32
    sa.sa_handler = hup_handler;
    sigemptyset(&sa.sa_mask);
#ifdef SA_RESTART
    sa.sa_flags = SA_RESTART;   /* Restart functions if
                                   interrupted by handler */
#endif
#ifdef SIGINT
    if (sigaction(SIGINT, &sa, NULL) == -1)
        printf("SIGINT signal handler not installed\n");
#endif
#ifdef SIGHUP
    if (sigaction(SIGHUP, &sa, NULL) == -1)
        printf("SIGHUP signal handler not installed\n");
#endif
#ifdef SIGTERM
    if (sigaction(SIGTERM, &sa, NULL) == -1)
        printf("SIGTERM signal handler not installed\n");
#endif
#endif

    // call g_type_init or otherwise we can crash
    gtk_init(&argc, &argv);
    if (!g_thread_supported())
        g_thread_init(NULL);

    uri = g_strdup_printf("%s/gnome-mplayer/cover_art", g_get_user_config_dir());
    if (!g_file_test(uri, G_FILE_TEST_IS_DIR)) {
        g_mkdir_with_parents(uri, 0775);
    }
    g_free(uri);

    uri = g_strdup_printf("%s/gnome-mplayer/plugin", g_get_user_config_dir());
    if (!g_file_test(uri, G_FILE_TEST_IS_DIR)) {
        g_mkdir_with_parents(uri, 0775);
    }
    g_free(uri);
    uri = NULL;

    default_playlist = g_strdup_printf("file://%s/gnome-mplayer/default.pls", g_get_user_config_dir());
    safe_to_save_default_playlist = TRUE;

    gm_store = gm_pref_store_new("gnome-mplayer");
    gmp_store = gm_pref_store_new("gecko-mediaplayer");
    vo = gm_pref_store_get_string(gm_store, VO);
    audio_device.alsa_mixer = gm_pref_store_get_string(gm_store, ALSA_MIXER);
    use_hardware_codecs = gm_pref_store_get_boolean(gm_store, USE_HARDWARE_CODECS);
    use_crystalhd_codecs = gm_pref_store_get_boolean(gm_store, USE_CRYSTALHD_CODECS);
    osdlevel = gm_pref_store_get_int(gm_store, OSDLEVEL);
    pplevel = gm_pref_store_get_int(gm_store, PPLEVEL);
#ifndef HAVE_ASOUNDLIB
    volume = gm_pref_store_get_int(gm_store, VOLUME);
#endif
    audio_channels = gm_pref_store_get_int(gm_store, AUDIO_CHANNELS);
    use_hw_audio = gm_pref_store_get_boolean(gm_store, USE_HW_AUDIO);
    fullscreen = gm_pref_store_get_boolean(gm_store, FULLSCREEN);
    softvol = gm_pref_store_get_boolean(gm_store, SOFTVOL);
    remember_softvol = gm_pref_store_get_boolean(gm_store, REMEMBER_SOFTVOL);
    volume_softvol = gm_pref_store_get_float(gm_store, VOLUME_SOFTVOL);
    volume_gain = gm_pref_store_get_int(gm_store, VOLUME_GAIN);
    forcecache = gm_pref_store_get_boolean(gm_store, FORCECACHE);
    vertical_layout = gm_pref_store_get_boolean(gm_store, VERTICAL);
    playlist_visible = gm_pref_store_get_boolean(gm_store, SHOWPLAYLIST);
    details_visible = gm_pref_store_get_boolean(gm_store, SHOWDETAILS);
    show_notification = gm_pref_store_get_boolean(gm_store, SHOW_NOTIFICATION);
    show_status_icon = gm_pref_store_get_boolean(gm_store, SHOW_STATUS_ICON);
    showcontrols = gm_pref_store_get_boolean_with_default(gm_store, SHOW_CONTROLS, showcontrols);
    restore_controls = showcontrols;
    disable_deinterlace = gm_pref_store_get_boolean(gm_store, DISABLEDEINTERLACE);
    disable_framedrop = gm_pref_store_get_boolean(gm_store, DISABLEFRAMEDROP);
    disable_fullscreen = gm_pref_store_get_boolean(gm_store, DISABLEFULLSCREEN);
    disable_context_menu = gm_pref_store_get_boolean(gm_store, DISABLECONTEXTMENU);
    disable_ass = gm_pref_store_get_boolean(gm_store, DISABLEASS);
    disable_embeddedfonts = gm_pref_store_get_boolean(gm_store, DISABLEEMBEDDEDFONTS);
    disable_pause_on_click = gm_pref_store_get_boolean(gm_store, DISABLEPAUSEONCLICK);
    disable_animation = gm_pref_store_get_boolean(gm_store, DISABLEANIMATION);
    auto_hide_timeout = gm_pref_store_get_int_with_default(gm_store, AUTOHIDETIMEOUT, auto_hide_timeout);
    disable_cover_art_fetch = gm_pref_store_get_boolean(gm_store, DISABLE_COVER_ART_FETCH);
    use_mediakeys = gm_pref_store_get_boolean_with_default(gm_store, USE_MEDIAKEYS, use_mediakeys);
    use_defaultpl = gm_pref_store_get_boolean_with_default(gm_store, USE_DEFAULTPL, use_defaultpl);
    metadata_codepage = gm_pref_store_get_string(gm_store, METADATACODEPAGE);

    alang = gm_pref_store_get_string(gm_store, AUDIO_LANG);
    slang = gm_pref_store_get_string(gm_store, SUBTITLE_LANG);

    subtitlefont = gm_pref_store_get_string(gm_store, SUBTITLEFONT);
    subtitle_scale = gm_pref_store_get_float(gm_store, SUBTITLESCALE);
    if (subtitle_scale < 0.25) {
        subtitle_scale = 1.0;
    }
    subtitle_codepage = gm_pref_store_get_string(gm_store, SUBTITLECODEPAGE);
    subtitle_color = gm_pref_store_get_string(gm_store, SUBTITLECOLOR);
    subtitle_outline = gm_pref_store_get_boolean(gm_store, SUBTITLEOUTLINE);
    subtitle_shadow = gm_pref_store_get_boolean(gm_store, SUBTITLESHADOW);
    subtitle_margin = gm_pref_store_get_int(gm_store, SUBTITLE_MARGIN);
    subtitle_fuzziness = gm_pref_store_get_int(gm_store, SUBTITLE_FUZZINESS);
    showsubtitles = gm_pref_store_get_boolean_with_default(gm_store, SHOW_SUBTITLES, TRUE);

    qt_disabled = gm_pref_store_get_boolean(gmp_store, DISABLE_QT);
    real_disabled = gm_pref_store_get_boolean(gmp_store, DISABLE_REAL);
    wmp_disabled = gm_pref_store_get_boolean(gmp_store, DISABLE_WMP);
    dvx_disabled = gm_pref_store_get_boolean(gmp_store, DISABLE_DVX);
    midi_disabled = gm_pref_store_get_boolean(gmp_store, DISABLE_MIDI);
    embedding_disabled = gm_pref_store_get_boolean(gmp_store, DISABLE_EMBEDDING);
    disable_embedded_scaling = gm_pref_store_get_boolean(gmp_store, DISABLE_EMBEDDED_SCALING);
    if (embed_window == 0) {
        single_instance = gm_pref_store_get_boolean(gm_store, SINGLE_INSTANCE);
        if (single_instance) {
            replace_and_play = gm_pref_store_get_boolean(gm_store, REPLACE_AND_PLAY);
            bring_to_front = gm_pref_store_get_boolean(gm_store, BRING_TO_FRONT);
        }
    }
    enable_global_menu = gm_pref_store_get_boolean(gm_store, ENABLE_GLOBAL_MENU);
    if (!enable_global_menu) {
        enable_global_menu = (g_getenv("UBUNTU_MENUPROXY") == NULL ? FALSE : TRUE);
    }

    enable_nautilus_plugin = gm_pref_store_get_boolean_with_default(gm_store, ENABLE_NAUTILUS_PLUGIN, TRUE);

    mplayer_bin = gm_pref_store_get_string(gm_store, MPLAYER_BIN);
    if (mplayer_bin != NULL && !g_file_test(mplayer_bin, G_FILE_TEST_EXISTS)) {
        g_free(mplayer_bin);
        mplayer_bin = NULL;
    }
    mplayer_dvd_device = gm_pref_store_get_string(gm_store, MPLAYER_DVD_DEVICE);
    extraopts = gm_pref_store_get_string(gm_store, EXTRAOPTS);
    use_xscrnsaver = gm_pref_store_get_boolean_with_default(gm_store, USE_XSCRNSAVER, use_xscrnsaver);

    accelerator_keys = gm_pref_store_get_string(gm_store, ACCELERATOR_KEYS);
    accel_keys = g_strv_new(KEY_COUNT);
    accel_keys_description = g_strv_new(KEY_COUNT);
    if (accelerator_keys != NULL) {
        parse = g_strsplit(accelerator_keys, " ", KEY_COUNT);
        for (i = 0; i < g_strv_length(parse); i++) {
            accel_keys[i] = g_strdup(parse[i]);
        }
        g_free(accelerator_keys);
        g_strfreev(parse);
    }
    assign_default_keys();
    accel_keys_description[FILE_OPEN_LOCATION] = g_strdup(_("Open Location"));
    accel_keys_description[EDIT_SCREENSHOT] = g_strdup(_("Take Screenshot"));
    accel_keys_description[EDIT_PREFERENCES] = g_strdup(_("Preferences"));
    accel_keys_description[VIEW_PLAYLIST] = g_strdup(_("Playlist"));
    accel_keys_description[VIEW_INFO] = g_strdup(_("Media Info"));
    accel_keys_description[VIEW_DETAILS] = g_strdup(_("Details"));
    accel_keys_description[VIEW_METER] = g_strdup(_("Audio Meter"));
    accel_keys_description[VIEW_FULLSCREEN] = g_strdup(_("Full Screen"));
    accel_keys_description[VIEW_ASPECT] = g_strdup(_("Aspect"));
    accel_keys_description[VIEW_SUBTITLES] = g_strdup(_("Subtitles"));
    accel_keys_description[VIEW_DECREASE_SIZE] = g_strdup(_("Decrease Subtitle Size"));
    accel_keys_description[VIEW_INCREASE_SIZE] = g_strdup(_("Increase Subtitle Size"));
    accel_keys_description[VIEW_ANGLE] = g_strdup(_("Switch Angle"));
    accel_keys_description[VIEW_CONTROLS] = g_strdup(_("Controls"));

    remember_loc = gm_pref_store_get_boolean(gm_store, REMEMBER_LOC);
    loc_window_x = gm_pref_store_get_int(gm_store, WINDOW_X);
    loc_window_y = gm_pref_store_get_int(gm_store, WINDOW_Y);
    loc_window_height = gm_pref_store_get_int(gm_store, WINDOW_HEIGHT);
    loc_window_width = gm_pref_store_get_int(gm_store, WINDOW_WIDTH);
    loc_panel_position = gm_pref_store_get_int(gm_store, PANEL_POSITION);

    keep_on_top = gm_pref_store_get_boolean(gm_store, KEEP_ON_TOP);
    resize_on_new_media = gm_pref_store_get_boolean(gm_store, RESIZE_ON_NEW_MEDIA);
    mouse_wheel_changes_volume = gm_pref_store_get_boolean_with_default(gm_store, MOUSE_WHEEL_CHANGES_VOLUME, FALSE);

    audio_device_name = gm_pref_store_get_string(gm_store, AUDIO_DEVICE_NAME);
    audio_device.description = g_strdup(audio_device_name);

    context = g_option_context_new(_("[FILES...] - GNOME Media player based on MPlayer"));
#ifdef GTK2_12_ENABLED
    g_option_context_set_translation_domain(context, "UTF-8");
    g_option_context_set_translate_func(context, (GTranslateFunc) gettext, NULL, NULL);
#endif
    g_option_context_add_main_entries(context, entries, GETTEXT_PACKAGE);
    g_option_context_add_group(context, gtk_get_option_group(TRUE));
    g_option_context_parse(context, &argc, &argv, &error);
    g_option_context_free(context);

    if (new_instance)
        single_instance = FALSE;

    if (verbose == 0)
        verbose = gm_pref_store_get_int(gm_store, VERBOSE);

    if (reallyverbose)
        verbose = 2;

    if (verbose) {
        printf(_("GNOME MPlayer v%s\n"), VERSION);
        printf(_("gmtk v%s\n"), gmtk_version());
    }

    if (cache_size == 0)
        cache_size = gm_pref_store_get_int(gm_store, CACHE_SIZE);
    if (cache_size == 0)
        cache_size = 2000;

    plugin_audio_cache_size = gm_pref_store_get_int(gm_store, PLUGIN_AUDIO_CACHE_SIZE);
    if (plugin_audio_cache_size == 0)
        plugin_audio_cache_size = 2000;

    plugin_video_cache_size = gm_pref_store_get_int(gm_store, PLUGIN_VIDEO_CACHE_SIZE);
    if (plugin_video_cache_size == 0)
        plugin_video_cache_size = 2000;

    if (control_id != 0)
        cache_size = plugin_video_cache_size;

    gm_pref_store_free(gm_store);
    gm_pref_store_free(gmp_store);

    if (verbose && embed_window) {
        printf("embedded in window id 0x%x\n", embed_window);
    }

    if (verbose && single_instance) {
        printf("Running in single instance mode\n");
    }
#ifdef GIO_ENABLED
    if (verbose) {
        printf("Running with GIO support\n");
    }
#endif
#ifdef ENABLE_PANSCAN
    if (verbose) {
        printf("Running with panscan enabled (mplayer svn r29565 or higher required)\n");
    }
#endif
    if (verbose) {
        printf("Using audio device: %s\n", audio_device_name);
    }

    if (softvol) {
        if (verbose)
            printf("Using MPlayer Software Volume control\n");
        if (remember_softvol && volume_softvol != -1) {
            if (verbose)
                printf("Using last volume of %f%%\n", volume_softvol * 100.0);
            volume = (gdouble) volume_softvol *100.0;
        } else {
            volume = 100.0;
        }
    }

    if (large_buttons)
        button_size = GTK_ICON_SIZE_DIALOG;

    if (playlist_visible && control_id != 0)
        playlist_visible = FALSE;

    if (error != NULL) {
        printf("%s\n", error->message);
        printf(_("Run 'gnome-mplayer --help' to see a full list of available command line options.\n"));
        return 1;
    }
    // if (verbose)
    //      printf("Threading support enabled = %i\n",g_thread_supported());

    if (rpconsole == NULL)
        rpconsole = g_strdup("NONE");

    // setup playliststore
    playliststore =
        gtk_list_store_new(N_COLUMNS, G_TYPE_STRING, G_TYPE_STRING, G_TYPE_INT, G_TYPE_INT,
                           G_TYPE_STRING, G_TYPE_STRING, G_TYPE_STRING, G_TYPE_FLOAT, G_TYPE_STRING,
                           G_TYPE_STRING, G_TYPE_POINTER, G_TYPE_STRING, G_TYPE_STRING,
                           G_TYPE_STRING, G_TYPE_INT, G_TYPE_INT, G_TYPE_INT, G_TYPE_INT,
                           G_TYPE_FLOAT, G_TYPE_FLOAT, G_TYPE_BOOLEAN);

    // only use dark theme if not embedded, otherwise use the default theme  
#ifdef GTK3_ENABLED
    if (embed_window <= 0) {
        gtk_settings = gtk_settings_get_default();
        g_object_set(G_OBJECT(gtk_settings), "gtk-application-prefer-dark-theme", TRUE, NULL);
    }
#endif

    create_window(embed_window);

    autopause = FALSE;
#ifdef GIO_ENABLED
    idledata->caching = g_mutex_new();
    idledata->caching_complete = g_cond_new();
#endif

    retrieve_metadata_pool = g_thread_pool_new(retrieve_metadata, NULL, 10, TRUE, NULL);

    if (argv[fileindex] != NULL) {
#ifdef GIO_ENABLED
        file = g_file_new_for_commandline_arg(argv[fileindex]);
        stat_result = -1;
        if (file != NULL) {
            GError *error = NULL;
            GFileInfo *file_info = g_file_query_info(file, G_FILE_ATTRIBUTE_UNIX_MODE, 0, NULL, &error);
            if (file_info != NULL) {
                buf.st_mode = g_file_info_get_attribute_uint32(file_info, G_FILE_ATTRIBUTE_UNIX_MODE);
                stat_result = 0;
                g_object_unref(file_info);
            }
            if (error != NULL) {
                if (verbose)
                    printf("failed to get mode: %s\n", error->message);
                g_error_free(error);
            }
            g_object_unref(file);
        }
#else
        stat_result = g_stat(argv[fileindex], &buf);
#endif

        if (verbose) {
            printf("opening %s\n", argv[fileindex]);
            printf("stat_result = %i\n", stat_result);
            printf("is block %i\n", S_ISBLK(buf.st_mode));
            printf("is character %i\n", S_ISCHR(buf.st_mode));
            printf("is reg %i\n", S_ISREG(buf.st_mode));
            printf("is dir %i\n", S_ISDIR(buf.st_mode));
            printf("playlist %i\n", playlist);
            printf("embedded in window id 0x%x\n", embed_window);
        }
        if (stat_result == 0 && S_ISBLK(buf.st_mode)) {
            // might have a block device, so could be a DVD

#ifdef HAVE_SYS_MOUNT_H
            fp = setmntent("/etc/mtab", "r");
            do {
                mnt = getmntent(fp);
                if (mnt)
                    printf("%s is at %s\n", mnt->mnt_fsname, mnt->mnt_dir);
                if (argv[fileindex] != NULL && mnt && mnt->mnt_fsname != NULL) {
                    if (strcmp(argv[fileindex], mnt->mnt_fsname) == 0)
                        break;
                }
            }
            while (mnt);
            endmntent(fp);
#endif
            if (mnt && mnt->mnt_dir) {
                printf("%s is mounted on %s\n", argv[fileindex], mnt->mnt_dir);
                uri = g_strdup_printf("%s/VIDEO_TS", mnt->mnt_dir);
                stat(uri, &buf);
                g_free(uri);
                if (S_ISDIR(buf.st_mode)) {
                    add_item_to_playlist("dvdnav://", 0);
                    gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter);
                    gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_DVD);
                    //play_iter(&iter, 0);
                    playiter = TRUE;
                } else {
                    uri = g_strdup_printf("file://%s", mnt->mnt_dir);
                    create_folder_progress_window();
                    add_folder_to_playlist_callback(uri, NULL);
                    g_free(uri);
                    destroy_folder_progress_window();
                    if (random_order) {
                        gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter);
                        randomize_playlist(playliststore);
                    }
                    if (gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter)) {
                        // play_iter(&iter, 0);
                        playiter = TRUE;
                    }
                }
            } else {
                parse_cdda("cdda://");
                if (random_order) {
                    gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter);
                    randomize_playlist(playliststore);
                }
                //play_file("cdda://", playlist);
                if (gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter)) {
                    // play_iter(&iter, 0);
                    playiter = TRUE;
                }
            }
        } else if (stat_result == 0 && S_ISDIR(buf.st_mode)) {
            uri = g_strdup_printf("%s/VIDEO_TS", argv[fileindex]);
            stat_result = g_stat(uri, &buf);
            g_free(uri);
            if (stat_result == 0 && S_ISDIR(buf.st_mode)) {
                add_item_to_playlist("dvdnav://", 0);
                gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter);
                gmtk_media_player_set_media_type(GMTK_MEDIA_PLAYER(media), TYPE_DVD);
                //play_iter(&iter, 0);
                playiter = TRUE;
            } else {
                create_folder_progress_window();
                uri = NULL;
#ifdef GIO_ENABLED
                file = g_file_new_for_commandline_arg(argv[fileindex]);
                if (file != NULL) {
                    uri = g_file_get_uri(file);
                    g_object_unref(file);
                }
#else
                uri = g_filename_to_uri(argv[fileindex], NULL, NULL);
#endif
                add_folder_to_playlist_callback(uri, NULL);
                g_free(uri);
                destroy_folder_progress_window();
                if (random_order) {
                    gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter);
                    randomize_playlist(playliststore);
                }
                if (gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter)) {
                    //play_iter(&iter, 0);
                    playiter = TRUE;
                }
            }
        } else {
            // local file
            // detect if playlist here, so even if not specified it can be picked up
            i = fileindex;

            while (argv[i] != NULL) {
                if (verbose > 1)
                    printf("Argument %i is %s\n", i, argv[i]);
#ifdef GIO_ENABLED
                if (!device_name(argv[i])) {
                    file = g_file_new_for_commandline_arg(argv[i]);
                    if (file != NULL) {
                        uri = g_file_get_uri(file);
                        g_object_unref(file);
                    } else {
                        uri = g_strdup(argv[i]);
                    }
                } else {
                    uri = g_strdup(argv[i]);
                }
#else
                uri = g_filename_to_uri(argv[i], NULL, NULL);
#endif

                if (uri != NULL) {
                    if (playlist == 0)
                        playlist = detect_playlist(uri);
                    if (!playlist) {
                        add_item_to_playlist(uri, playlist);
                    } else {
                        if (!parse_playlist(uri)) {
                            add_item_to_playlist(uri, playlist);
                        }
                    }
                    g_free(uri);
                }
                i++;
            }

            if (random_order) {
                gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter);
                randomize_playlist(playliststore);
            }
            if (gtk_tree_model_get_iter_first(GTK_TREE_MODEL(playliststore), &iter)) {
                // play_iter(&iter, 0);
                playiter = TRUE;
            }
        }

    }
#ifdef HAVE_GPOD
    if (load_tracks_from_gpod) {
        gpod_mount_point = find_gpod_mount_point();
        printf("mount point is %s\n", gpod_mount_point);
        if (gpod_mount_point != NULL) {
            gpod_load_tracks(gpod_mount_point);
        } else {
            printf("Unable to find gpod mount point\n");
        }
    }
#endif

    gm_audio_update_device(&audio_device);
    gm_audio_get_volume(&audio_device);
    gm_audio_set_server_volume_update_callback(&audio_device, set_volume);
    set_media_player_attributes(media);

    if (!softvol) {
        if (pref_volume != -1) {
            audio_device.volume = (gdouble) pref_volume / 100.0;
        }
        if (verbose)
            printf("The volume on '%s' is %f\n", audio_device.description, audio_device.volume);
        volume = audio_device.volume * 100;
    } else {
        audio_device.volume = volume / 100.0;
    }
#ifdef GTK2_12_ENABLED
    gtk_scale_button_set_value(GTK_SCALE_BUTTON(vol_slider), audio_device.volume);
#else
    gtk_range_set_value(GTK_RANGE(vol_slider), audio_device.volume);
#endif
    use_volume_option = detect_volume_option();

    dbus_hookup(embed_window, control_id);
    show_window(embed_window);

    if (playiter)
        play_iter(&iter, 0);

    if (argv[fileindex] == NULL && embed_window == 0) {
        // When running as apple.com external player, don't load the default playlist
        if (control_id == 0) {
            use_remember_loc = remember_loc;
            gtk_check_menu_item_set_active(GTK_CHECK_MENU_ITEM(menuitem_view_playlist), playlist_visible);
        } else {
            remember_loc = FALSE;
            use_remember_loc = FALSE;
            // prevents saving of a playlist with one item on it
            use_defaultpl = FALSE;
            // don't save the loc when launched with a single file
            save_loc = FALSE;
        }
    } else {
        // prevents saving of a playlist with one item on it
        use_defaultpl = FALSE;
        // don't save the loc when launched with a single file
        save_loc = FALSE;
    }

    if (single_instance && embed_window == 0) {
        if (control_id == 0) {
            use_remember_loc = remember_loc;
            gtk_check_menu_item_set_active(GTK_CHECK_MENU_ITEM(menuitem_view_playlist), playlist_visible);
        }
    }

    if (embed_window == 0) {
        if (remember_loc) {
            gtk_window_move(GTK_WINDOW(window), loc_window_x, loc_window_y);
            g_idle_add(set_pane_position, NULL);

        }
    }

    safe_to_save_default_playlist = FALSE;
    if (use_defaultpl) {
        create_folder_progress_window();
        parse_playlist(default_playlist);
        destroy_folder_progress_window();
    }
    safe_to_save_default_playlist = TRUE;

    gtk_main();

    return 0;
}
