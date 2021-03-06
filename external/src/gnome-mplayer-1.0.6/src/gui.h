/* -*- Mode: C; indent-tabs-mode: t; c-basic-offset: 4; tab-width: 4 -*- */
/*
 * gui.h
 * Copyright (C) Kevin DeKorte 2006 <kdekorte@gmail.com>
 *
 * gui.h is free software.
 *
 * You may redistribute it and/or modify it under the terms of the
 * GNU General Public License, as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * gui.h is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with gui.h.  If not, write to:
 * 	The Free Software Foundation, Inc.,
 * 	51 Franklin Street, Fifth Floor
 * 	Boston, MA  02110-1301, USA.
 */
//#include <X11/Xlib.h>

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif
#include <gtk/gtk.h>
#include <gdk/gdk.h>
#ifdef X11_ENABLED
#include <gdk/gdkx.h>
#endif
#include <gmlib.h>
#include <gmtk.h>
#include <gdk/gdkkeysyms.h>
#include <glib.h>
#include <glib/gstdio.h>
#include <glib/gi18n.h>
#include <math.h>
#ifdef HAVE_ASOUNDLIB
#include <asoundlib.h>
#endif

#include "playlist.h"
GtkWidget *window;
GdkWindow *window_container;
GtkWidget *fs_window;
GtkWidget *fs_controls;

GtkWidget *menubar;
GtkMenuItem *menuitem_file;
GtkMenu *menu_file;
GtkMenuItem *menuitem_file_open;
GtkMenuItem *menuitem_file_open_folder;
GtkMenuItem *menuitem_file_open_location;
GtkMenuItem *menuitem_file_disc;
GtkMenu *menu_file_disc;
GtkMenuItem *menuitem_file_open_acd;
GtkMenuItem *menuitem_file_open_sep1;
GtkMenuItem *menuitem_file_open_dvd;
GtkMenuItem *menuitem_file_open_dvdnav;
GtkMenuItem *menuitem_file_open_dvd_folder;
GtkMenuItem *menuitem_file_open_dvdnav_folder;
GtkMenuItem *menuitem_file_open_dvd_iso;
GtkMenuItem *menuitem_file_open_dvdnav_iso;
GtkMenuItem *menuitem_file_open_sep2;
GtkMenuItem *menuitem_file_open_vcd;

GtkMenuItem *menuitem_file_tv;
GtkMenu *menu_file_tv;
GtkMenuItem *menuitem_file_open_atv;
GtkMenuItem *menuitem_file_open_dtv;
GtkMenuItem *menuitem_file_open_ipod;
GtkMenuItem *menuitem_file_recent;
GtkWidget *menuitem_file_recent_items;
GtkMenuItem *menuitem_file_sep2;
GtkMenuItem *menuitem_file_quit;

GtkMenuItem *menuitem_edit;
GtkMenu *menu_edit;
GtkMenuItem *menuitem_edit_random;
GtkMenuItem *menuitem_edit_loop;
GtkWidget *repeat;
GtkWidget *shuffle;
GtkMenuItem *menuitem_edit_switch_audio;
GtkMenuItem *menuitem_edit_set_audiofile;
GtkMenuItem *menuitem_edit_select_audio_lang;
GtkMenuItem *menuitem_edit_set_subtitle;
GtkMenuItem *menuitem_edit_select_sub_lang;
GtkMenuItem *menuitem_lang;
GtkWidget *tracks;
GtkWidget *subtitles;


GtkMenuItem *menuitem_edit_take_screenshot;
GtkMenuItem *menuitem_edit_sep1;
GtkMenuItem *menuitem_edit_config;
GtkMenuItem *menuitem_help;
GtkMenuItem *menuitem_view;
GtkMenu *menu_view;
GtkMenuItem *menuitem_view_playlist;
GtkMenuItem *menuitem_view_info;
GtkMenuItem *menuitem_view_details;
GtkMenuItem *menuitem_view_meter;
GtkMenuItem *menuitem_view_sep0;
GtkMenuItem *menuitem_view_fullscreen;
GtkMenuItem *menuitem_view_sep1;
GtkMenuItem *menuitem_view_onetoone;
GtkMenuItem *menuitem_view_twotoone;
GtkMenuItem *menuitem_view_onetotwo;
GtkMenuItem *menuitem_view_onetoonepointfive;
GtkMenuItem *menuitem_view_sep4;
GtkMenuItem *menuitem_view_aspect;
GtkMenu *menu_view_aspect;
GtkMenuItem *menuitem_view_aspect_default;
GtkMenuItem *menuitem_view_aspect_four_three;
GtkMenuItem *menuitem_view_aspect_sixteen_nine;
GtkMenuItem *menuitem_view_aspect_sixteen_ten;
GtkMenuItem *menuitem_view_aspect_follow_window;
GtkMenuItem *menuitem_view_sep2;
GtkMenuItem *menuitem_view_subtitles;
GtkMenuItem *menuitem_view_smaller_subtitle;
GtkMenuItem *menuitem_view_larger_subtitle;
GtkMenuItem *menuitem_view_decrease_subtitle_delay;
GtkMenuItem *menuitem_view_increase_subtitle_delay;
GtkMenuItem *menuitem_view_sep5;
GtkMenuItem *menuitem_view_angle;
GtkMenuItem *menuitem_view_controls;
GtkMenuItem *menuitem_view_sep3;
GtkMenuItem *menuitem_view_advanced;
GtkMenu *menu_help;
GtkMenuItem *menuitem_help_about;


GtkMenu *popup_menu;
GtkMenuItem *menuitem_open;
GtkMenuItem *menuitem_sep3;
GtkMenuItem *menuitem_play;
GtkMenuItem *menuitem_stop;
GtkMenuItem *menuitem_prev;
GtkMenuItem *menuitem_next;
GtkMenuItem *menuitem_sep1;
GtkMenuItem *menuitem_copyurl;
GtkMenuItem *menuitem_sep2;
GtkMenuItem *menuitem_sep4;
GtkMenuItem *menuitem_save;
GtkMenuItem *menuitem_showcontrols;
GtkMenuItem *menuitem_fullscreen;
GtkMenuItem *menuitem_config;
GtkMenuItem *menuitem_quit;
gulong delete_signal_id;

GtkWidget *vbox_master;
GtkWidget *pane;
GtkWidget *vbox;
GtkWidget *hbox;
GtkWidget *controls_box;

GtkWidget *media;
GtkWidget *media_hbox;
GtkWidget *media_label;
GtkWidget *cover_art;
GtkWidget *audio_meter;
GtkWidget *details_vbox;
GtkWidget *details_table;

GtkWidget *details_video_size;
GtkWidget *details_video_format;
GtkWidget *details_video_codec;
GtkWidget *details_video_fps;
GtkWidget *details_video_bitrate;
GtkWidget *details_video_chapters;
GtkWidget *details_audio_format;
GtkWidget *details_audio_codec;
GtkWidget *details_audio_channels;
GtkWidget *details_audio_bitrate;
GtkWidget *details_audio_samplerate;

GdkPixbuf *pb_icon;
GdkPixbuf *pb_button;

GList *icon_list;

GtkWidget *button_event_box;
GtkWidget *image_button;

GtkWidget *play_button;
GtkWidget *stop_button;
//GtkWidget *pause_button;
GtkWidget *ff_button;
GtkWidget *rew_button;

GtkWidget *play_event_box;
//GtkWidget *pause_event_box;
GtkWidget *stop_event_box;
GtkWidget *ff_event_box;
GtkWidget *rew_event_box;
GtkWidget *prev_event_box;
GtkWidget *next_event_box;
GtkWidget *menu_event_box;

GtkWidget *fs_event_box;
//GtkProgressBar *progress;
GmtkMediaTracker *tracker;
GtkWidget *vol_slider;

gboolean in_button;

GtkWidget *image_play;
GtkWidget *image_pause;
GtkWidget *image_stop;
GtkWidget *image_ff;
GtkWidget *image_rew;
GtkWidget *image_next;
GtkWidget *image_prev;
GtkWidget *image_menu;
GtkWidget *image_fs;
GtkWidget *image_icon;

#ifdef GTK2_12_ENABLED
GtkStatusIcon *status_icon;
GtkWidget *config_show_status_icon;
#else
GtkTooltips *tooltip;
GtkTooltips *volume_tip;
#endif

GtkWidget *config_window;

GtkWidget *config_vo;
GtkWidget *config_hardware_codecs;
GtkWidget *config_crystalhd_codecs;
GtkWidget *config_ao;
GtkWidget *config_mixer;
GtkWidget *config_audio_channels;
GtkWidget *config_use_hw_audio;
GtkWidget *config_volume;
GtkWidget *config_cachesize;
GtkWidget *config_plugin_audio_cache_size;
GtkWidget *config_plugin_video_cache_size;
GtkWidget *config_osdlevel;
GtkWidget *config_deinterlace;
GtkWidget *config_framedrop;
GtkWidget *config_pplevel;

GtkWidget *config_playlist_visible;
GtkWidget *config_details_visible;
GtkWidget *config_vertical_layout;
GtkWidget *config_single_instance;
GtkWidget *config_replace_and_play;
GtkWidget *config_bring_to_front;
GtkWidget *config_resize_on_new_media;
GtkWidget *config_pause_on_click;
GtkWidget *config_softvol;
GtkWidget *config_remember_softvol;
GtkWidget *config_volume_gain;
GtkWidget *config_forcecache;
GtkWidget *config_verbose;
GtkWidget *config_show_notification;
GtkWidget *config_use_xscrnsaver;
GtkWidget *config_use_mediakeys;
GtkWidget *config_use_defaultpl;
GtkWidget *config_disable_animation;
GtkWidget *config_mouse_wheel;
GtkWidget *config_enable_nautilus_plugin;

GtkWidget *config_alang;
GtkWidget *config_slang;
GtkWidget *config_metadata_codepage;

GtkWidget *config_ass;
GtkWidget *config_embeddedfonts;
GtkWidget *config_subtitle_font;
GtkWidget *config_subtitle_scale;
GtkWidget *config_subtitle_codepage;
GtkWidget *config_subtitle_color;
GtkWidget *config_subtitle_outline;
GtkWidget *config_subtitle_shadow;
GtkWidget *config_subtitle_margin;
GtkWidget *config_subtitle_fuzziness;
GtkWidget *config_show_subtitles;

GtkWidget *config_qt;
GtkWidget *config_real;
GtkWidget *config_wmp;
GtkWidget *config_dvx;
GtkWidget *config_midi;
GtkWidget *config_noembed;
GtkWidget *config_noscaling;

GtkWidget *config_mplayer_bin;
GtkWidget *config_mplayer_dvd_device;
GtkWidget *config_extraopts;
GtkWidget *config_remember_loc;
GtkWidget *config_keep_on_top;

GtkWidget *open_location;

GtkWidget *folder_progress_window;
GtkWidget *folder_progress_label;
GtkWidget *folder_progress_bar;

// Playlist container
GtkWidget *plvbox;
GSList *lang_group;
GSList *audio_group;

// Video Settings
GtkWidget *adv_brightness;
GtkWidget *adv_contrast;
GtkWidget *adv_gamma;
GtkWidget *adv_hue;
GtkWidget *adv_saturation;

GtkAccelGroup *accel_group;

glong last_movement_time;

gboolean popup_handler(GtkWidget * widget, GdkEvent * event, void *data);
gboolean delete_callback(GtkWidget * widget, GdkEvent * event, void *data);
void config_close(GtkWidget * widget, void *data);

gboolean rew_callback(GtkWidget * widget, GdkEventExpose * event, void *data);
gboolean play_callback(GtkWidget * widget, GdkEventExpose * event, void *data);
gboolean pause_callback(GtkWidget * widget, GdkEventExpose * event, void *data);
gboolean stop_callback(GtkWidget * widget, GdkEventExpose * event, void *data);
gboolean ff_callback(GtkWidget * widget, GdkEventExpose * event, void *data);
gboolean prev_callback(GtkWidget * widget, GdkEventExpose * event, void *data);
gboolean next_callback(GtkWidget * widget, GdkEventExpose * event, void *data);
void vol_slider_callback(GtkRange * range, gpointer user_data);
gboolean fs_callback(GtkWidget * widget, GdkEventExpose * event, void *data);
gboolean make_panel_and_mouse_visible(gpointer data);
void menuitem_open_callback(GtkMenuItem * menuitem, void *data);
void menuitem_quit_callback(GtkMenuItem * menuitem, void *data);
void menuitem_about_callback(GtkMenuItem * menuitem, void *data);
void menuitem_play_callback(GtkMenuItem * menuitem, void *data);
void menuitem_pause_callback(GtkMenuItem * menuitem, void *data);
void menuitem_stop_callback(GtkMenuItem * menuitem, void *data);
void menuitem_fs_callback(GtkMenuItem * menuitem, void *data);
void menuitem_showcontrols_callback(GtkCheckMenuItem * menuitem, void *data);
void menuitem_quit_callback(GtkMenuItem * menuitem, void *data);
void menuitem_details_callback(GtkMenuItem * menuitem, void *data);
void menuitem_view_decrease_subtitle_delay_callback(GtkMenuItem * menuitem, void *data);
void menuitem_view_increase_subtitle_delay_callback(GtkMenuItem * menuitem, void *data);
void clear_playlist(GtkWidget * widget, void *data);
void create_playlist_widget();
void add_folder_to_playlist(GtkWidget * widget, void *data);
gboolean playlist_drop_callback(GtkWidget * widget, GdkDragContext * dc,
                                gint x, gint y, GtkSelectionData * selection_data, guint info, guint t, gpointer data);
void create_folder_progress_window();
void destroy_folder_progress_window();
void update_status_icon();
void setup_accelerators();
gboolean set_software_volume(gdouble * data);
gboolean set_adjust_layout(gpointer data);
gboolean get_key_and_modifier(gchar * keyval, guint * key, GdkModifierType * modifier);
gboolean accel_key_key_press_event(GtkWidget * widget, GdkEventKey * event, gpointer data);
void assign_default_keys();
void reset_keys_callback(GtkButton * button, gpointer data);
gint get_index_from_key_and_modifier(guint key, GdkModifierType modifier);

void show_fs_controls();
void hide_fs_controls();
gboolean set_destroy(gpointer data);
