<template>
  <section class="medma-section">
    <div class="medma-container">
      <div class="medma-panel">
        <div class="medma-upload-input">
          <button
            type="button"
            class="btn btn-default"
            style="display: inline-block; position: relative;"
            @click="onClickInputFile()"
          >
            Add New
          </button>
          <input
            type="file"
            ref="inputFile"
            style="display: none"
            multiple
            @change="onChangeInput($event.target)"
            accept="image/tiff, image/gif, image/jpeg, image/x-png, image/x-rgb, image/x-MS-bmp, image/x-photo-cd, image/x-portable-pixmap, image/x-portablebitmap, image/x-portable-greymap"
          />
        </div>
        <div class="medma-filter">
          <div class="medma-filter-form">
            <select class="form-control">
              <option value="">All Media Items</option>
              <option value="">Images</option>
              <option value="">Video</option>
              <option value="">Audio</option>
              <option value="">Document</option>
            </select>
            <input
              type="search"
              class="form-control"
              placeholder="Search media items..."
            />
          </div>
        </div>
      </div>
      <div class="medma-columns">
        <div class="medma-column" v-for="(file, i) in listFiles">
          <div
            :data-id="file.id"
            class="medma-thumbnail"
            ref="thumbnail"
            @click="selected(i, $event)"
            @click.shift.ctrl="multiSelected(i)"
            @dblclick="getImageDetail(file, i)"
          >
            <!-- <div
            :data-id="file.id"
            :data-file="file"
            class="medma-thumbnail"
            ref="thumbnail"
            @click="openDetail()"
          > -->
            <div class="medma-image">
              <div class="medma-zxvf"></div>
              <img :src="file.thumbnailUrl" alt="" />
            </div>
            <div class="medma-caption">
              <div class="medma-type">
                <img :src="getIconsTypeFile(file.mime)" :alt="file.mime" />
              </div>
              <div class="medma-text">{{ file.name }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="medma-up" v-show="uploadFile.length">
      <div class="medma-up-header">
        <div class="medma-up-title">
          <p v-if="successed">{{ successed }} Upload Selesai</p>
        </div>
        <div class="medma-up-action">
          <div
            class="medma-qrre"
            @click="expandFile = !expandFile"
            v-bind:data-tooltip="[expandFile ? 'Minimalkan' : 'Maksimalkan']"
          >
            <div class="medma-tuxz">
              <svg
                v-if="expandFile"
                x="0px"
                y="0px"
                width="14px"
                height="14px"
                viewBox="0 0 24 24"
                focusable="false"
              >
                <path
                  fill="#FFFFFF"
                  d="M21.17,5.17L12,14.34l-9.17-9.17L0,8l12,12,12-12z"
                ></path>
              </svg>
              <svg
                v-if="!expandFile"
                x="0px"
                y="0px"
                width="14px"
                height="14px"
                viewBox="0 0 24 24"
                focusable="false"
              >
                <path
                  fill="#FFFFFF"
                  d="M2.83,18.83L12,9.66l9.17,9.17L24,16,12,4,0,16z"
                ></path>
              </svg>
            </div>
          </div>
          <div class="medma-qrre" data-tooltip="Close">
            <div class="medma-tuxz" @click="clearUpload()">
              <svg
                x="0px"
                y="0px"
                width="14px"
                height="14px"
                viewBox="0 0 10 10"
                focusable="false"
              >
                <polygon
                  class="a-s-fa-Ha-pa"
                  fill="#FFFFFF"
                  points="10,1.01 8.99,0 5,3.99 1.01,0 0,1.01 3.99,5 0,8.99 1.01,10 5,6.01 8.99,10 10,8.99 6.01,5 "
                ></polygon>
              </svg>
            </div>
          </div>
        </div>
      </div>
      <div class="medma-werwq">
        <div
          class="medma-aHDs"
          v-for="(file, i) in uploadFile"
          v-show="expandFile"
        >
          <div
            class="medma-up-content"
            @mouseover="mouseover(i)"
            @mouseleave="mouseleave(i)"
          >
            <div class="medma-upc-content">
              <div class="medma-upc-icon">
                <img :src="file.type" />
              </div>
              <div class="medma-upc-title">
                {{ file.name }}
              </div>
            </div>

            <div class="medma-up-action">
              <div
                class="medma-qrre medma-qXZs"
                v-if="file.status == 2"
                data-tooltip="Berhasil"
              >
                <div class="medma-tuxz">
                  <svg
                    class="a-s-fa-Ha-pa"
                    width="24px"
                    height="24px"
                    viewBox="0 0 24 24"
                    fill="#0F9D58"
                  >
                    <path
                      d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"
                      style="fill:#0f9d58"
                    ></path>
                  </svg>
                </div>
              </div>
              <div
                class="medma-qrre medma-qXZs"
                @click="abortRequest(file)"
                v-if="file.status == 1"
                data-tooltip="Batal Upload"
              >
                <!-- {{file[i]}} -->

                <transition name="hover">
                  <div class="medma-tuxz">
                    <svg
                      x="0px"
                      y="0px"
                      width="24px"
                      height="24px"
                      viewBox="0 0 24 24"
                      focusable="false"
                    >
                      <path
                        class="a-s-fa-Ha-pa"
                        fill="#000000"
                        d="M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10c5.5,0,10-4.5,10-10S17.5,2,12,2z M17,15.6L15.6,17L12,13.4L8.4,17L7,15.6l3.6-3.6   L7,8.4L8.4,7l3.6,3.6L15.6,7L17,8.4L13.4,12L17,15.6z"
                      ></path>
                    </svg>
                  </div>
                </transition>
                <transition name="hover">
                  <div class="medma-tuxz" v-show="!file.hovered">
                    <div class="dial">
                      <div class="dial__progress" ref="progress"></div>
                      <div class="dial__progress_bg" data-percent="100"></div>
                      <div class="dial__content"></div>
                    </div>
                  </div>
                </transition>
              </div>
              <div
                class="medma-qrre medma-qXZs"
                v-if="file.status == 0"
                @click="uploadingFile(files[i], i, false)"
                data-tooltip="Upload Ulang"
              >
                <div class="medma-tuxz">
                  <svg
                    x="0px"
                    y="0px"
                    width="24px"
                    height="24px"
                    viewBox="0 0 24 24"
                    focusable="false"
                  >
                    <path
                      class="a-s-fa-Ha-pa"
                      fill="#000000"
                      d="M17.6,6.4C16.2,4.9,14.2,4,12,4c-4.4,0-8,3.6-8,8s3.6,8,8,8c3.7,0,6.8-2.6,7.7-6h-2.1c-0.8,2.3-3,4-5.6,4    c-3.3,0-6-2.7-6-6s2.7-6,6-6c1.7,0,3.1,0.7,4.2,1.8L13,11h7V4L17.6,6.4z"
                    ></path>
                  </svg>
                </div>
              </div>
              <div
                class="medma-qrre medma-qXZs"
                v-if="file.status == 3"
                data-tooltip="Gagal Upload"
              >
                <div class="medma-tuxz">
                  <svg
                    class="a-s-fa-Ha-pa"
                    width="24px"
                    height="24px"
                    viewBox="0 0 24 24"
                    fill="red"
                  >
                    <path
                      d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"
                    ></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="medma-detail-image" v-show="detail" ref="medma_detail">
      <div class="medma-XZ-HAS" v-if="detail">
        <div class="medma-ZVaS">
          <div class="medma-xxzd">
            <span>Attachment Details</span>
          </div>
          <div class="medma-Kdad">
            <button class="dashicons" @click="moveImageWithArrow('left')">
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
            <button class="dashicons" @click="moveImageWithArrow('right')">
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
            <button class="dashicons" @click="detail = false">
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
        <transition :name="transition">
          <div class="medma-vXZd" v-if="file.unique">
            <div class="medma-LkXa">
              <img :src="file.url" alt="" class="medma-vmxZ" />
            </div>
            <div class="medma-XAvz">
              <div class="medma-Xadf">
                <div class="medma-xa-sXd">
                  <strong>File name: </strong>
                  {{ file.name }}
                </div>
                <div class="medma-xa-sXd">
                  <strong>File type: </strong>
                  {{ file.mime }}
                </div>
                <div class="medma-xa-sXd">
                  <strong>Uploaded on: </strong>
                  {{ file.created_at }}
                </div>
                <div class="medma-xa-sXd">
                  <strong>File size: </strong>
                  {{ file.size | getFileSize }} kb
                </div>
                <div class="medma-xa-sXd">
                  <strong>Dimensions: </strong>
                  {{ file.dimensions }}
                </div>
              </div>
              <div class="medma-Vx-xss">
                <div class="medma-Gzdf">
                  <strong>Title</strong>
                  <input type="text" class="medma-dffs" v-model="file.title" />
                </div>
                <div class="medma-Gzdf">
                  <strong>Caption / Alt</strong>
                  <textarea class="medma-dffs" v-model="file.alt"></textarea>
                </div>
                <div class="medma-Gzdf">
                  <strong>Source</strong>
                  <input type="text" class="medma-dffs" v-model="file.source" />
                </div>
                <div class="medma-Gzdf">
                  <strong>Uploaded By</strong>
                  <input type="text" class="medma-dffs" disabled />
                </div>
                <div class="medma-Gzdf">
                  <strong>Link</strong>
                  <input
                    type="text"
                    class="medma-dffs"
                    readonly
                    v-model="file.url"
                  />
                </div>
              </div>
              <div class="medma-XV-ss">
                <button
                  type="button"
                  class="button-link delete-attachment"
                  @click="deleteAttachment(file.unique)"
                  ref="delete"
                >
                  Delete Permanently
                </button>
                <img
                  src="/img/loader.svg"
                  style="visibility: hidden"
                  ref="deleteLoader"
                />
                |
                <button
                  type="button"
                  class="button-link delete-attachment"
                  @click="updateDescription(file.unique)"
                >
                  Save Description
                </button>
                <img
                  src="/img/loader.svg"
                  style="visibility: hidden"
                  ref="saveLoader"
                />
              </div>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";
const CancelToken = axios.CancelToken;
const source = CancelToken.source();
export default {
  data() {
    return {
      files: [],
      fetchFiles: [],
      listFiles: [],
      expandFile: true,
      uploadFile: [],
      detail: false,
      file: {},
      hovered: [],
      progress: [],
      successed: 0,
      transition: ""
    };
  },
  methods: {
    mouseover(i) {
      this.uploadFile[i].hovered = true;
    },
    mouseleave(i) {
      this.uploadFile[i].hovered = false;
    },
    closeDetails() {},
    getListFiles() {
      axios.get("/media/all").then(e => {
        this.listFiles = e.data;
      }).catch(({response: {status, statusText}}) => {
          if(status == 500){
              return this.$toast.error(statusText)
          }
          this.$toast.error(data)
      })
    },
    onClickInputFile() {
      return this.$refs.inputFile.click();
    },
    onChangeInput(e) {
      let files = e.files;
      if (!files.length) return false;
      this.fetchingFiles(files);
    },
    async fetchingFiles(files) {
      Array.from(files).forEach((f, i) => {
        if (
          !f.type.match("image.*") &&
          !f.type.match("video.*") &&
          !type.match("audio.*")
        ) {
          console.log(`${f.name} is not image`);
          return;
        }
        this.uploadingFile(f, i + this.files.length);
      });
      this.files = this.files.concat(files);
    },
    uploadingFile(file, i, multiple = true) {
      const vm = this;
      let cancel;
      const FData = new FormData();
      FData.append("file", file);

      axios
        .post("/media/upload", FData, {
          headers: {
            "Content-Type": "multipart/form-data`"
          },
          onUploadProgress: progressEvent => {
            let progress = Math.round(
              (progressEvent.loaded * 100) / progressEvent.total
            );
            this.$refs.progress[i].setAttribute("data-percent", progress);
          },
          cancelToken: new CancelToken(function executor(c) {
            cancel = c;
          })
        })
        .then(e => {
          this.uploadFile[i].status = 2;
          this.listFiles.push(e.data);
          this.successed += 1;
        })
        .catch(({response: {status, statusText}}) => {
            this.$refs.saveLoader.style.visibility = "hidden";
            if(status == 500){
                return this.$toast.error(statusText)
            }
            this.$toast.error(data)
            this.uploadFile[i].status = 3;
        })
      if (multiple) {
        this.uploadFile.push({
          name: file.name,
          type: this.getIconsTypeFile(file.type),
          status: 1,
          abort: cancel,
          hovered: false
        });
      } else {
        this.uploadFile[i].status = 1;
        this.uploadFile[i].name = file.name;
        this.uploadFile[i].type = this.getIconsTypeFile(file.type);
        this.uploadFile[i].abort = cancel;
        this.uploadFile[i].hovered = false;
      }
    },
    abortRequest(file) {
      file.abort();
      file.status = 0;
    },
    clearUpload() {
      this.uploadFile.map(e => {
        if (e.status == 1) {
          e.abort();
        }
      });
      this.files = [];
      this.uploadFile = [];
    },
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
    multiSelected(i) {
      let thumblist = this.$refs.thumbnail;
      if (thumblist[i].classList.contains("medma-zs-XAd")) {
        return thumblist[i].classList.remove("medma-zs-XAd");
      } else {
        thumblist[i].classList.add("medma-zs-XAd");
      }
    },
    getImageDetail(file, j) {
      let thumblist = this.$refs.thumbnail;
      for (var i = 0; i < thumblist.length; i++) {
        let contain = thumblist[i].classList.contains("medma-zs-XAd");

        if (contain && i != j) thumblist[i].classList.remove("medma-zs-XAd");
      }
      thumblist[j].classList.add("medma-zs-XAd");
      axios.get("/media/" + file.unique).then(e => {
        this.file = e.data;
        this.openPopup();
      }).catch(({response: {status, statusText}}) => {
          if(status == 500){
              return this.$toast.error(statusText)
          }
          this.$toast.error(data)
      })
    },
    openPopup() {
      this.detail = true;

      if (this.detail) {
        let popup = this.$refs.medma_detail.classList;
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
    updateDescription(unique) {
      this.$refs.saveLoader.style.visibility = "visible";
      axios.put("/media/" + unique, this.file).then(({ data }) => {
        this.file = data.data;
        const {
          data: { name, id, mime, unique, url }
        } = data;
        let index = this.listFiles.findIndex(e => {
          if (e.unique == unique) {
            return e;
          }
        });
        this.listFiles[index] = {
          name,
          id,
          unique,
          thumbnailUrl: url + "?s=thumbnail",
          mime
        };
        this.$refs.saveLoader.style.visibility = "hidden";
        this.$toast.success(data.message);
      })
      .catch(({response: {status, statusText}}) => {
          this.$refs.saveLoader.style.visibility = "hidden";
          if(status == 500){
              return this.$toast.error(statusText)
          }
          this.$toast.error(data)
      })
    },
    moveImageWithArrow(arrow) {
      if (!arrow) return false;
      let index = this.listFiles.findIndex(e => {
        if (e.unique == this.file.unique) {
          return e;
        }
      });
      if (arrow == "right") {
        this.transition = "slide-right";
        if (index + 1 >= this.listFiles.length) {
          index = 0;
        } else {
          index = index + 1;
        }
      }
      if (arrow == "left") {
        this.transition = "slide-left";
        if (index - 1 <= 0) {
          index = this.listFiles.length - 1;
        } else {
          index = index - 1;
        }
      }
      this.file = {};
      axios.get("/media/" + this.listFiles[index].unique).then(e => {
        setTimeout(() => {
          this.file = e.data;
        }, 300);
      }).catch(({response: {status, statusText}}) => {
          if(status == 500){
              return this.$toast.error(statusText)
          }
          this.$toast.error(data)
      })
    },
    deleteAttachment(unique) {
      let index = this.listFiles.findIndex(e => {
        if (e.unique == unique) {
          return e;
        }
      });
      this.$refs.deleteLoader.style.visibility = "visible";
      setTimeout(() => {
        axios
          .delete("/media/" + unique)
          .then(e => {
            this.listFiles.splice(index, 1);
            this.detail = false;
            this.$refs.deleteLoader.style.visibility = "hidden";
          })
          .catch(({response: {status, statusText}}) => {
              this.$refs.deleteLoader.style.visibility = "hidden";
              if(status == 500){
                  return this.$toast.error(statusText)
              }
              this.$toast.error(data)
          })
      }, 1000);
    }
  },
  filters: {
    getFileSize(size) {
      return size == 0 ? 0 : Math.round(size / 1024);
    }
  },
  mounted() {
    this.getListFiles();
  }
};
</script>

<style lang="css" scoped></style>
