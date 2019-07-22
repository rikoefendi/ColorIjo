<template>
  <div>
    <button class="btn btn-primary" @click="openFeatured">
      add featured
    </button>
    <div class="medma-detail-image" v-show="featured" ref="featured">
      <div class="medma-XZ-HAS" v-if="featured">
        <div class="medma-ZVaS">
          <div class="medma-xxzd">
            <span>Attachment Details</span>
          </div>
          <div class="medma-Kdad">
            <button class="dashicons">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
              >
                <path
                  fill="#72777c"
                  d="M16.67 0l2.83 2.829-9.339 9.175 9.339 9.167-2.83 2.829-12.17-11.996z"
                />
              </svg>
            </button>
            <button class="dashicons">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
              >
                <path
                  fill="#72777c"
                  d="M7.33 24l-2.83-2.829 9.339-9.175-9.339-9.167 2.83-2.829 12.17 11.996z"
                />
              </svg>
            </button>
            <button class="dashicons" @click="featured = false">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
              >
                <path
                  fill="#72777c"
                  d="M23 20.168l-8.185-8.187 8.185-8.174-2.832-2.807-8.182 8.179-8.176-8.179-2.81 2.81 8.186 8.196-8.186 8.184 2.81 2.81 8.203-8.192 8.18 8.192z"
                />
              </svg>
            </button>
          </div>
        </div>
        <div class="medma-vXZd">
          <div
            class="medma-LkXa"
            style="flex-direction: column;justify-content: flex-start;align-items: normal; overflow: auto; padding: 15px;"
            :class="[file.unique ? 'a-xAVd': 'Xdfs']"
          >
            <div class="medma-columns">
              <div class="medma-column" v-for="(file, i) in listFiles">
                <div
                  :data-id="file.id"
                  class="medma-thumbnail"
                  ref="thumbnail"
                  @click="getImageDetail(file, i)"
                >
                  <div class="medma-image">
                    <div class="medma-zxvf"></div>
                    <img :src="file.thumbnailUrl" alt="" />
                  </div>
                  <div class="medma-caption">
                    <div class="medma-type">
                      <img
                        :src="getIconsTypeFile(file.mime)"
                        :alt="file.mime"
                      />
                    </div>
                    <div class="medma-text">{{ file.name }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="medma-XAvz"
            v-if="file.unique"
            style="overflow: auto;"
          >
            <div class="medma-Xadf" style="text-align: center">
              <img :src="file.url" alt="" class="medma-vmxZ" />
            </div>
            <div class="medma-Vx-xss">
              <div class="medma-Gzdf">
                <strong>Title</strong>
                <input type="text" class="medma-control" v-model="file.title" />
              </div>
              <div class="medma-Gzdf">
                <strong>Caption / Alt</strong>
                <textarea class="medma-control" v-model="file.alt"></textarea>
              </div>
              <div class="medma-Gzdf">
                <strong>Source</strong>
                <input type="text" class="medma-control" v-model="file.source" />
              </div>
              <div class="medma-Gzdf">
                <strong>Uploaded By</strong>
                <input type="text" class="medma-control" disabled />
              </div>
              <div class="medma-Gzdf">
                <strong>Link</strong>
                <input
                  type="text"
                  class="medma-control"
                  readonly
                  v-model="file.url"
                />
              </div>
            </div>
            <div class="medma-XV-ss">
              <img
                src="/img/loader.svg"
                style="visibility: hidden"
                ref="deleteLoader"
              />
              <button type="button" class="button-link delete-attachment">
                Save Description
              </button>
              <img
                src="/img/loader.svg"
                style="visibility: hidden"
                ref="saveLoader"
              />
              |
              <button type="button" class="button-link delete-attachment" @click="chooseImage(file)">
                Pilih Gambar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { EventBus } from './event-bus.js';
export default {
  data() {
    return {
      listFiles: [],
      featured: false,
      file: {}
    };
  },
  methods: {
    getIconsTypeFile(type) {
      if (type.match("image.*")) {
        return "https://drive-thirdparty.googleusercontent.com/16/type/image/png";
      }
      if (type.match("video.*")) {
        return "https://drive-thirdparty.googleusercontent.com/16/type/video/x-matroska";
      }
      if (type.match("audio.*")) {
        return "https://drive-thirdparty.googleusercontent.com/16/type/mp3";
      }
      return "https://drive-thirdparty.googleusercontent.com/16/type/application/octet-stream";
    },
    getListFiles() {
      axios
        .get("/media/all")
        .then(e => {
          this.listFiles = e.data;
        })
        .catch(({ response: { status, statusText } }) => {
          if (status == 500) {
            return this.$toast.error(statusText);
          }
          this.$toast.error(data);
        });
    },
    selected(j, $event) {
      if ($event.shiftKey && $event.ctrlKey && $event.dblclick) return;
      let thumblist = this.$refs.thumbnail;
      for (var i = 0; i < thumblist.length; i++) {
        let contain = thumblist[i].classList.contains("medma-zs-XAd");

        if (contain && i != j) thumblist[i].classList.remove("medma-zs-XAd");
      }
      if (thumblist[j].classList.contains("medma-zs-XAd")) {
        thumblist[j].classList.remove("medma-zs-XAd");
      } else {
        thumblist[j].classList.add("medma-zs-XAd");
      }
    },
    openFeatured() {
      this.featured = true;

      if (this.featured) {
        let popup = this.$refs.featured.classList;
        document.addEventListener("click", e => {
          if (e.target.classList == popup) {
              this.file = {}
            this.detail = false;
          }
        });
        document.addEventListener("keyup", e => {
          if (e.code == "Escape") {
              this.file = {}
            this.detail = false;
          }
          if (e.code == "ArrowRight") {
            this.moveImageWithArrow("right");
          }
          if (e.code == "ArrowLeft") {
            this.moveImageWithArrow("left");
          }
        });
      }
    },
    getImageDetail(file, j) {
      let thumblist = this.$refs.thumbnail;
      for (var i = 0; i < thumblist.length; i++) {
        let contain = thumblist[i].classList.contains("medma-zs-XAd");

        if (contain && i != j) thumblist[i].classList.remove("medma-zs-XAd");
      }
      thumblist[j].classList.add("medma-zs-XAd");
      axios
        .get("/media/" + file.unique)
        .then(e => {
          this.file = e.data;
          this.openPopup();
        })
        .catch(({ response: { status, statusText } }) => {
          if (status == 500) {
            return this.$toast.error(statusText);
          }
          this.$toast.error(data);
        });
    },
    chooseImage(file){
        EventBus.$emit('choosedImage', file)
        this.featured = false
        this.file = {}
    }
  },
  mounted() {
    this.getListFiles();
  }
};
</script>

<style lang="css" scoped></style>
