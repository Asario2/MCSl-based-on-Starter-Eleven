<template>
        <!-- **Tabelle direkt unterhalb des Blog-Containers (fixiert)** -->
        <div class="w-full mt-2">
            <table :class="['w-full border-collapse mx-auto justify-center rounded-lg shadow-sm table-class',nostars ? 'NSMaTable' : 'MaTable']" style="max-width:300px;margin-bottom:18px;" @click.stop>
                <tbody>
                    <tr class="border-0"> <!-- Entfernt alle Ränder -->

                        <td class=" text-center" width="60%">
                            <button  @click.stop.prevent="openComments(postId)"
                                class="flex items-center gap-2 px-2 py-1 rounded-lg font-semibold bg-blue-500 text-white hover:bg-blue-600
                                    dark:bg-blue-600 dark:hover:bg-blue-700 dark:text-white text-center tog-tab" :data-post-id="postId">
                                <icon-comment /> Kommentare
                            </button>
                        </td>

                        <td class="p-1.5 text-center" width="40%">
                            <button @click.stop.prevent="toggleShareBox(postId)"
                                class="flex items-center gap-2 px-2 py-1 rounded-lg font-semibold bg-blue-500 text-white hover:bg-blue-600
                                    dark:bg-blue-600 dark:hover:bg-blue-700 dark:text-white text-center tog-tab" :data-post-id="postId">
                                <icon-share /> Teilen
                            </button>
                        </td>

                        <td class=" text-center" v-if="!nostars">
                            <button  @click.stop.prevent="toggleStarBox(postId)"
                                class="flex items-center gap-2 px-2 py-1 rounded-lg font-semibold bg-blue-500 text-white hover:bg-blue-600
                                    dark:bg-blue-600 dark:hover:bg-blue-700 dark:text-white text-center tog-tab"  :data-post-id="postId">
                                <icon-star we="16" he="16" /> Bewerten
                            </button>
                        </td>
                    </tr>

                    <!-- Kommentarbox -->

                    <tr v-if="showComments == postId">
                        <td colspan="3" class="p-4 h-auto align-top" style="z-index:49;"  :id="'commentBox_' + postId">

                            <div style="z-index:49;" class="w300 relative border border-gray-300 p-4 rounded-lg shadow-sm bg-white dark:bg-gray-800">
                                <button @click.stop.prevent="closeComments(this.postId)" class="absolute top-2 right-2 text-red-500 text-xl bold">
                                    <b>&times;</b>
                                </button>
                                <Comments :postId="postId" :showComments="this.showComments" />
                            </div>


                        </td>
                    </tr>

                    <!-- Teilen -->
                    <tr v-if="showShareBox[postId]">
                        <td colspan="3" class="p-4">
                            <div align="center" :ref="'shariff_' + postId" :added="urlAdded" id="shariff-container" class="shariff w-full w300 relative border border-gray-300 p-4 pb-2 rounded-lg shadow-sm bg-white dark:bg-gray-800" data-button-style="icon"></div>
                        </td>
                    </tr>

                    <!-- Bewertung -->
                    <tr v-if="showStarBox[postId]">
                        <td colspan="3" class="p-4">
                            <AddRating :postId="postId" :table="this.tablex" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

</template>

<script>
import { Link } from "@inertiajs/vue3";
import IconPlusCircle from "@/Application/Components/Icons/PlusCircle.vue";
import averageRating from "@/Application/Components/Social/averageratings.vue";
import IconPencil from "@/Application/Components/Icons/Pencil.vue";
import Comments from "@/Application/Components/Social/comments.vue";
import Share from "@/Application/Components/Social/share.vue";
import AddRating from "@/Application/Components/Social/addrating.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import IconCamera from "@/Application/Components/Icons/Camera.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import IconComment from "@/Application/Components/Icons/IconComment.vue";
import { throttle } from "lodash";
import { CleanTable, CleanId } from '@/helpers';
import mapValues from "lodash/mapValues";
import pickBy from "lodash/pickBy";
import IconEye from "@/Application/Components/Icons/Eye.vue";
import IconTrash from "@/Application/Components/Icons/Trash.vue";
import DisplayDate from "@/Application/Components/Content/DisplayDate.vue";
import he from "he";
import { nextTick } from "vue";
import { route } from "ziggy-js";
import IconShare from "@/Application/Components/Icons/IconShare.vue";
import IconStar from "@/Application/Components/Icons/IconStar.vue";
import { Boolean } from "php-parser";
export default {
    name: "SocialButtons",
    //
    components: {
        Link,
    DisplayDate,
    IconPencil,
    IconPlusCircle,
    editbtns,
    IconTrash,
    IconCamera,
    SearchFilter,
    Comments,
    Share,
    AddRating,
    IconComment,
    IconShare,
    IconStar,
    averageRating,
    },

    props: {
        blog: {
            type: Object,
        },
        aiOverlayImage: {
            type: String,
        },
        postId:{
            type: [Number,String]
        },
        sm:{
            type: String,
        },
        nostars:{
            type: Boolean,
            default: false,
        },
        slug:{
            type:String,
        },
        empty:{
            type:Boolean,
            default:false,
        },
        ublock:{
            type: String,
        },
    },
    data() {
    return {
        dma: localStorage.getItem('theme'),
        urlAdded:  this.urlAdder(this.postId),
        showShareBox: {} ,
        showStarBox: {}, // Wert aus localStorage speichern
        showComments: null, // Zustand für die Anzeige der Kommentarfunktion
    };
},
mounted(){
//   var $_GET = {};
//         if(document.location.toString().indexOf('?') !== -1) {
//             var query = document.location
//                         .toString()
//                         // get the query string
//                         .replace(/^.*?\?/, '')
//                         // and remove any existing hash string (thanks, @vrijdenker)
//                         .replace(/#.*$/, '')
//                         .split('&');

//             for(var i=0, l=query.length; i<l; i++) {
//             var aux = decodeURIComponent(query[i]).split('=');
//             $_GET[aux[0]] = aux[1];
//             }
//         }
// },
// setup(){
//      $_GET = {};
//         if(document.location.toString().indexOf('?') !== -1) {
//             var query = document.location
//                         .toString()
//                         // get the query string
//                         .replace(/^.*?\?/, '')
//                         // and remove any existing hash string (thanks, @vrijdenker)
//                         .replace(/#.*$/, '')
//                         .split('&');

//             for(var i=0, l=query.length; i<l; i++) {
//             var aux = decodeURIComponent(query[i]).split('=');
//             $_GET[aux[0]] = aux[1];
//             }
//         }
// },


},
methods:{
    openComments(id) {
        this.imageRemove(id);
        event.stopPropagation();
        this.showComments = this.showComments === id ? null : id;
        // console.log("showComments_alt2:", this.showComments);
        const ti = document.getElementById('teaser-img');
        if(ti)
        {
            const ti2 = document.getElementById('teaser-img2');
            if (ti.style.overflow === "hidden" || this.showStarBox || this.showShareBox ) {
                ti.style.overflow = "visible";
                ti2.style.overflow = "visible";
            } else {
                ti.style.overflow = "hidden";
                ti2.style.overflow = "hidden";
            }
        }


    },
    imageRemove(id){
        window.addEventListener('load', function () {
        const commentBox = document.getElementById("commentBox_" + id);
        if (commentBox) {
            const computedHeight = window.getComputedStyle(commentBox).height;
            // console.log(computedHeight); // Gibt die Höhe des Elements aus
        } else {
            //console.error("Element nicht gefunden! commentBox_"+id);
        }
    });

    },
    closeComments() {
        this.showComments = null;
    },

    reset() {
        this.form = mapValues(this.form, () => null);
    },
    toggleStarBox(id) {

    // console.log(`toggleStarBox wurde für ID ${id} aufgerufen`);

    // Sicherstellen, dass showShareBox existiert
    if (!this.showStarBox) {
        this.showStarBox = {};
    }

    this.showStarBox[id] = !this.showStarBox[id];
    const ti = document.getElementById('teaser-img');
    if(ti)
    {
        const ti2 = document.getElementById('teaser-img2');
        if (ti.style.overflow === "hidden" || this.showShareBox || this.showComments) {
            ti.style.overflow = "visible";
            ti2.style.overflow = "visible";
        } else {
            ti.style.overflow = "hidden";
            ti2.style.overflow = "hidden";
        }
    }

    },
    urlAdder(id){
        if(this.ublock)
        {
            const uname = this.ublock;
            return "/show/" + uname + "/" + this.postId
        }
        if(this.empty)
        {
            return "";
        }
        else if(CleanTable() != "blogs")
        {
            let p = ''
            if(this?.ex_page)
            {
                p = "?page=" + $_GET['page'];
            }
            if(this?.ex_search)
            {
                p += "&search=".$_GET?.['search'];
            }

            return p + "#st"+id;
        }
        else{
            return "/show/"+this.slug;
        }

    },
    toggleShareBox(id) {

    // console.log(`toggleShareBox wurde für ID ${id} aufgerufen`);

    // Sicherstellen, dass showShareBox existiert
    if (!this.showShareBox) {
        this.showShareBox = {};
    }
    const ti = document.getElementById('teaser-img');
    if(ti){

        const ti2 = document.getElementById('teaser-img2');
        if (ti.style.overflow === "hidden" || this.showStarBox || this.showComments) {
            ti.style.overflow = "visible";
            ti2.style.overflow = "visible";
        } else {
            ti.style.overflow = "hidden";
            ti2.style.overflow = "hidden";
        }
    }
    this.showShareBox[id] = !this.showShareBox[id];

    // console.log("showShareBox:", this.showShareBox);

    if (this.showShareBox[id]) {
        this.$nextTick(() => {
            const shariffRef = this.$refs['shariff_' + id];
            if (!shariffRef) {
                console.error(`Shariff-Element für ID ${id} nicht gefunden.`);
                return;
            }
            // console.log(`Shariff für ID ${id} wird initialisiert...`);
            this.initShariff(id);
        });
    } else {
        this.$nextTick(() => {
            const shariffRef = this.$refs['shariff_' + id];
            if (shariffRef) {
                // console.log(`Shariff-Inhalt für ID ${id} wird geleert.`);
                shariffRef.innerHTML = "";
            }
        });
    }
},

        initShariff(id) {
    nextTick(() => {
        const shariffRef = this.$refs['shariff_' + id];
        if (!shariffRef) {
            console.error(`Shariff-Element für ID ${id} nicht gefunden`);
            return;
        }
        const url = `${window.location.origin}${window.location.pathname}${this.urlAdded || ''}`;
        shariffRef.setAttribute('data-url', url);
        // console.log(url);

        // console.log(`Shariff wird für ID ${id} initialisiert`, shariffRef);
        new Shariff(shariffRef, {
            services: ["facebook", "telegram", "whatsapp", "xing", "twitter"],
            theme: "classic",
            orientation: "horizontal",
        });
    });


    },

    toggleCommentBox(postId) {
        this.imageRemove(postId);
        this.showComments = this.showComments === postId ? null : postId;

    },

    decodeEntities(text) {
        if (text) {
            text = text?.replace(/<br\s*\/?>/g, "\n");
            return he.decode(text);
        } else {
            return "";
        }
    },

    deleteDataRow(id) {
        if (confirm("Wollen Sie diesen Beitrag wirklich löschen?")) {
            axios
                .delete(this.routeDelete + id)
                .then(() => {
                    this.$emit("deleted");
                    this.$inertia.reload();
                })
                .catch((error) => {
                    console.error("Fehler beim Löschen:", error);
                });
        }
    },

    editDataRow(id) {
        const table = CleanTable();

        var rt = `/admin/tables/edit/${id}/images`;

        location.href = rt;
    },
},
mounted(){
this.urlAdded = this.urlAdder(this.postId);
this.imageRemove(this.postId);

},
showComments(newVal, oldVal) {
        if (newVal !== oldVal) {
            // Hier rufst du die Methode auf, wenn sich der Wert von `showComments` ändert
            this.imageRemove(this.postId);
            //alert(this.postId);
        }
    }
};
</script>
<style scoped>
#commentBox{
    max-width:300px !important;
    width:300px !important;
}
.shariff{
    margin-top:-11px;
    z-index:49;
}

@media screen and (max-width: 1000px) {
.MaTable{
margin-left:-58px;
}
.SmMaTable{
margin-left:-7px;
}
.MaTable_blogs{
margin-left:-38px;
}
.SmMaTable_blogs{
margin-left:-38px;
}
}
@media screen and (max-width: 361px) {
.MaTable{
margin-left:-68px !important;
}
}
@media screen and (min-width: 1024px) {
.Matable{
    margin-left:0px;
}
}
.w300{
    max-width:300px;
}
</style>
