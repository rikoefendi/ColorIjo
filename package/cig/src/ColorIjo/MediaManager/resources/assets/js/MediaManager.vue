<template>
  <section
    class="medma-section"
    oncontextmenu="return false"
    onselectstart="return false"
    ondragstart="return false"
    ref="medma_section"
  >
    <div class="medma-container">
      <div class="medma-panel">
        <div class="medma-upload-input">
          <button
            type="button"
            class="medma-button"
            style="display: inline-block; position: relative;"
            @click="onClickInputFile"
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
            <!-- <select class="medma-control">
              <option value="">All Media Items</option>
              <option value="">Images</option>
              <option value="">Video</option>
              <option value="">Audio</option>
              <option value="">Document</option>
            </select> -->
            <input
              type="search"
              class="medma-control"
              placeholder="Search media items..."
              v-model="search"
            />
          </div>
        </div>
      </div>
      <div class="medma-columns">
        <div
          class="medma-column"
          v-for="(file, i) in listingFiles"
          v-if="listFiles.data.length"
        >
          <div
            class="medma-thumbnail"
            ref="thumbnail"
            @click.shift="multiSelected(i)"
            @click="getImageDetail(i, $refs.thumbnail[i].dataset.unique)"
            @click.right="openContextMenu(i, $event, file)"
            :data-unique="file.unique"
            :data-index="i"
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
              <img :src="file.url.thumb" alt="" />
            </div>
            <div class="medma-caption">
              <div class="medma-type">
                <img :src="getIconsTypeFile(file.mime)" :alt="file.mime" />
              </div>
              <div class="medma-text">{{ file.name }}</div>
            </div>
          </div>
        </div>
        <div class="medma-column" style="justify-content:center; top: 38%; bottom: 50%;">
        <infinite-loading @distance="1" @infinite="infiniteHandler"> </infinite-loading>
        </div>
      </div>
    </div>
    <div class="medma-up" v-show="fileUploadForm.uploadFile.length">
      <div class="medma-up-header">
        <div class="medma-up-title">
          <p v-if="fileUploadForm.text">{{ fileUploadForm.text }}</p>
        </div>
        <div class="medma-up-action">
          <div
            class="medma-qrre"
            @click="fileUploadForm.expandFile = !fileUploadForm.expandFile"
            v-bind:data-tooltip="[
              fileUploadForm.expandFile ? 'Minimalkan' : 'Maksimalkan'
            ]"
          >
            <div class="medma-tuxz">
              <svg
                v-if="fileUploadForm.expandFile"
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
                v-if="!fileUploadForm.expandFile"
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
          <div class="medma-qrre" data-tooltip="Close" @click="clearUpload()">
            <div class="medma-tuxz">
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
          v-for="(file, i) in fileUploadForm.uploadFile"
          v-show="fileUploadForm.expandFile"
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

            <div class="medma-up-action action-upload">
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
                      <div class="dial__progress upload-progress"></div>
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
    <div
      class="medma-detail-image"
      ref="medma_detail"
      style="opacity: 0; display: none;"
    >
      <div class="" v-if="!detail"></div>
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
            <button class="dashicons" @click="closeDetail">
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
              <img
                :src="file.url.maxresdefault"
                :alt="file.alt"
                class="medma-vmxZ"
              />
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
                  <input
                    type="text"
                    class="medma-control"
                    v-model="file.title"
                  />
                </div>
                <div class="medma-Gzdf">
                  <strong>Caption / Alt</strong>
                  <textarea class="medma-control" v-model="file.alt"></textarea>
                </div>
                <div class="medma-Gzdf">
                  <strong>Source</strong>
                  <input
                    type="text"
                    class="medma-control"
                    v-model="file.source"
                  />
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
                    v-model="file.url.original"
                  />
                </div>
              </div>
              <div class="medma-XV-ss">
                <button
                  type="button"
                  class="button-link delete-attachment"
                  @click="deleteAttachment(file.unique, false)"
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
    <div
      class="medma-right-click-menu"
      tabindex="-1"
      ref="contextmenu"
      id="contextmenu"
      v-show="contextmenu"
    >
      <div v-if="contextmenu" ref="contextmenuContent">
        <div
          class="medma-nN-DD"
          v-on:click.left="
            getImageDetail(
              $refs.contextmenu.dataset.index,
              $refs.contextmenu.dataset.unique
            )
          "
        >
          <div class="medma-BDfd">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
            >
              <path
                d="M12.015 7c4.751 0 8.063 3.012 9.504 4.636-1.401 1.837-4.713 5.364-9.504 5.364-4.42 0-7.93-3.536-9.478-5.407 1.493-1.647 4.817-4.593 9.478-4.593zm0-2c-7.569 0-12.015 6.551-12.015 6.551s4.835 7.449 12.015 7.449c7.733 0 11.985-7.449 11.985-7.449s-4.291-6.551-11.985-6.551zm-.015 5c1.103 0 2 .897 2 2s-.897 2-2 2-2-.897-2-2 .897-2 2-2zm0-2c-2.209 0-4 1.792-4 4 0 2.209 1.791 4 4 4s4-1.791 4-4c0-2.208-1.791-4-4-4z"
              />
            </svg>
          </div>
          <div class="medma-Mx-df">
            View
          </div>
        </div>
        <div
          class="medma-nN-DD"
          @click="downloadFile($refs.contextmenu.dataset.unique)"
        >
          <div class="medma-BDfd">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
            >
              <path
                d="M12 21l-8-9h6v-12h4v12h6l-8 9zm9-1v2h-18v-2h-2v4h22v-4h-2z"
              />
            </svg>
          </div>
          <div class="medma-Mx-df">
            Download
          </div>
        </div>
        <div class="medma-nN-DD" @click="deleteAttachment(fileChoosed, true)">
          <div class="medma-BDfd">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
            >
              <path
                d="M9 19c0 .552-.448 1-1 1s-1-.448-1-1v-10c0-.552.448-1 1-1s1 .448 1 1v10zm4 0c0 .552-.448 1-1 1s-1-.448-1-1v-10c0-.552.448-1 1-1s1 .448 1 1v10zm4 0c0 .552-.448 1-1 1s-1-.448-1-1v-10c0-.552.448-1 1-1s1 .448 1 1v10zm5-17v2h-20v-2h5.711c.9 0 1.631-1.099 1.631-2h5.315c0 .901.73 2 1.631 2h5.712zm-3 4v16h-14v-16h-2v18h18v-18h-2z"
              />
            </svg>
          </div>
          <div class="medma-Mx-df">
            Delete
          </div>
        </div>
      </div>
    </div>
    <div class="Xh-FSd" id="questionpopup" style="display: none;">
      <div class="bA-Bd">
        <div class="Xc-nV">
          <div class="aB-Hnd">
            Batalkan semua upload?
          </div>
          <button class="Kl-mn">
            <svg
              class="a-qd-va"
              x="0px"
              y="0px"
              width="10px"
              height="10px"
              viewBox="0 0 10 10"
              focusable="false"
            >
              <polygon
                class="a-s-fa-Ha-pa"
                fill="#000000"
                points="10,1.01 8.99,0 5,3.99 1.01,0 0,1.01 3.99,5 0,8.99 1.01,10 5,6.01 8.99,10 10,8.99 6.01,5 "
              ></polygon>
            </svg>
          </button>
        </div>
        <div class="gH-Fd">
          Upload Anda tidak selesai. Anda ingin membatalkan upload?
        </div>
        <div class="kN-Bw">
          <button
            class="medma-button button-blue button-no-outline"
            type="button"
          >
            Lanjutkan Upload
          </button>
          <button
            class="medma-button button-no-outline"
            type="button"
            @click="clearUpload(false)"
          >
            Batalkan Upload
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
Vue.component('infinite-loading', require('vue-infinite-loading').default);
import axios from "axios";
const CancelToken = axios.CancelToken;
const source = CancelToken.source();
export default {
  data() {
    return {
        page: 1,
      files: [],
      listFiles: {
          data: []
      },
      fileUploadForm: {
        text: "",
        success: 0,
        expandFile: true,
        uploadFile: []
      },
      detail: false,
      transition: "",
      file: {},
      fileChoosed: [],
      contextmenu: false,
      multiple: false,
      search: "",
      queeUpload: 0,
    };
  },
  computed: {
    listingFiles() {
          return this.listFiles.data
    }
  },
  methods: {
    orderBy(sorKey) {
      this.sortKey = sorKey;
      this.sortSettings[sorKey] = !this.sortSettings[sorKey];
      this.desc = this.sortSettings[sorKey];
    },
    mouseover(i) {
      this.fileUploadForm.uploadFile[i].hovered = true;
    },
    mouseleave(i) {
      this.fileUploadForm.uploadFile[i].hovered = false;
    },
    infiniteHandler($state) {
        let url = '/media/all?page='+this.page
      this.$axios
        .get(url)
        .then(e => {
                e.data.data.forEach(e => this.listFiles.data.push(e))
                this.listFiles.next_page_url = e.data.next_page_url
                this.listFiles.last_page_url = e.data.last_page_url
                this.listFiles.prev_page_url = e.data.prev_page_url
                this.listFiles.current_page = e.data.current_page
                this.listFiles.from = e.data.from
                this.listFiles.last_page = e.data.last_page
                this.listFiles.per_page = e.data.per_page
                this.listFiles.to = e.data.to
                this.listFiles.total = e.data.total
                $state.loaded()
                if(this.listFiles.last_page == this.page){
                    $state.complete()
                }
                this.page += 1
        })
        .catch(({ response: { status, statusText, data } }) => {
          if (status == 500) {
            return this.$toast.error(statusText);
          }
          Object.keys(data).forEach(e => {
            this.$toast.error(data[e]);
          });
        });

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
      await Array.from(files).forEach((f, i) => {
          this.files.push(f)
        if (
          !f.type.match("image.*") &&
          !f.type.match("video.*") &&
          !type.match("audio.*")
        ) {
          console.log(`${f.name} is not image`);
          return;
        }
        this.fileUploadForm.uploadFile.push({
          name: f.name,
          type: this.getIconsTypeFile(f.type),
          status: 1,
          abort: '',
          hovered: false
        });
      });
      setTimeout(() => {
          this.uploadingFile(files[this.queeUpload], this.queeUpload)
      }, 1000)
    },
    uploadingFile(file, i) {
        file = file ? file: this.files[i]
        console.log(file);
      const vm = this;
      let cancel;
      const FData = new FormData();
      FData.append("file", file);
      this.$axios
        .post("/media/upload", FData, {
          headers: {
            "Content-Type": "multipart/form-data`"
          },
          onUploadProgress: progressEvent => {
            let progress = Math.round(
              (progressEvent.loaded * 100) / progressEvent.total
            );
            // let upAct = document.getElementsByClassName('action-upload')
            let upAct = document
              .getElementsByClassName("action-upload")
              [i].getElementsByClassName("upload-progress");
            if (upAct) {
              upAct[0].setAttribute("data-percent", progress);
            }
          },
          cancelToken: new CancelToken(function executor(c) {
            cancel = c;
          })
        })
        .then(e => {
          this.fileUploadForm.uploadFile[i].status = 2;
          this.listFiles.data.push(e.data);
          this.fileUploadForm.success += 1;
          let text = " Upload Selesai";
          this.fileUploadForm.text = this.fileUploadForm.success + text;
          this.queeUpload += 1;
          if(this.queeUpload < this.files.length){
              console.log(this.files[this.queeUpload]);
              this.uploadingFile(this.files[this.queeUpload], this.queeUpload)
          }
        })
        .catch(({ response }) => {
            this.queeUpload += 1;
            if(this.queeUpload < this.files.length){
                this.uploadingFile(this.files[this.queeUpload], this.queeUpload)
            }
          if (response.status == 500) {
            return this.$toast.error(response.statusText);
          }
          Object.keys(response.data).forEach(e => {
            this.$toast.error(response.data[e]);
          });
          this.fileUploadForm.uploadFile[i].status = 3;
        });

        this.fileUploadForm.uploadFile[i].status = 1;
        this.fileUploadForm.uploadFile[i].name = file.name;
        this.fileUploadForm.uploadFile[i].type = this.getIconsTypeFile(
          file.type
        );
        this.fileUploadForm.uploadFile[i].abort = cancel;
        this.fileUploadForm.uploadFile[i].hovered = false;
    },
    abortRequest(file) {
      file.abort();
      file.status = 0;
    },
    clearUpload(isCheck = true) {
      let question = document.getElementById("questionpopup");
      if (isCheck) {
        question.style.opacity = 0;
        this.fileUploadForm.uploadFile.map(e => {
          if (e.status == 1) {
            setTimeout(() => {
              question.style.opacity = 1;
              question.style.display = "flex";
            }, 200);
            // e.abort();
          } else {
            this.fileUploadForm.uploadFile = [];
            this.files = [];
          }
        });
      }
      if (!isCheck) {
        this.fileUploadForm.uploadFile.map(e => {
          if (e.status == 1) {
            setTimeout(() => {
              question.style.opacity = 1;
              question.style.display = "none";
            }, 200);
            e.abort();
          }
        });
        this.fileUploadForm.uploadFile = [];
        this.files = [];
      }
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
    selected(j) {
      let thumblist = this.$refs.thumbnail;
      this.fileChoosed = [
        { [thumblist[j].dataset.index]: thumblist[j].dataset.unique }
      ];
      for (var i = 0; i < thumblist.length; i++) {
        let contain = thumblist[i].classList.contains("medma-zs-XAd");

        if (contain && i != j) thumblist[i].classList.remove("medma-zs-XAd");
      }
      thumblist[j].classList.add("medma-zs-XAd");
    },
    multiSelected(i) {
      let thumblist = this.$refs.thumbnail;
      let fileC = this.fileChoosed;
      if (thumblist[i].classList.contains("medma-zs-XAd")) {
        this.fileChoosed = fileC.filter(e => {
          if (e != thumblist[i].dataset.unique) {
            return e;
          }
        });
        return thumblist[i].classList.remove("medma-zs-XAd");
      } else {
        this.fileChoosed.push({
          [thumblist[i].dataset.index]: thumblist[i].dataset.unique
        });
        thumblist[i].classList.add("medma-zs-XAd");
      }
      // console.log(this.fileChoosed);
    },
    getImageDetail(i, file) {
      if (this.multiple) return false;
      let unique = typeof file == "object" ? file.unique : file;
      this.selected(i, unique);
      this.$axios
        .get("/media/" + unique + "?size=maxresdefault")
        .then(e => {
          this.file = e.data;
          this.openPopup();
          this.contextmenu = false;
        })
        .catch(({ response: { status, statusText, data } }) => {
          if (status == 500) {
            return this.$toast.error(statusText);
          }
          Object.keys(data).forEach(e => {
            this.$toast.error(data[e]);
          });
        });
    },
    openPopup() {
      this.detail = true;

      if (this.detail) {
        let body = document.querySelector("body > div[id=app]").parentElement;
        body.style.overflow = "hidden";
        let popup = this.$refs.medma_detail;
        popup.style.opacity = 1;
        popup.style.display = "flex";
        document.addEventListener("click", e => {
          if (e.target.classList == popup.classList) {
            body.style.overflow = "auto";
            this.file = {};
            this.detail = false;
            popup.style.opacity = 0;
            popup.style.display = "none";
          }
        });
        document.addEventListener("keyup", e => {
          if (e.code == "Escape") {
            body.style.overflow = "auto";
            this.file = {};
            this.detail = false;
            popup.style.opacity = 0;
            popup.style.display = "none";
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
      this.$axios
        .put("/media/" + unique, this.file)
        .then(({ data }) => {
          this.file = data.data;
          const {
            data: { name, id, mime, unique, url }
          } = data;
          let index = this.listFiles.data.findIndex(e => {
            if (e.unique == unique) {
              return e;
            }
          });
          this.listFiles.data[index] = {
            name,
            id,
            unique,
            url,
            mime
          };
          this.$refs.saveLoader.style.visibility = "hidden";
          this.$toast.success(data.message);
        })
        .catch(({ response: { status, statusText, data } }) => {
          this.$refs.saveLoader.style.visibility = "hidden";
          if (status == 500) {
            return this.$toast.error(statusText);
          }
          Object.keys(data).forEach(e => {
            this.$toast.error(data[e]);
          });
        });
    },
    moveImageWithArrow(arrow) {
      if (!arrow) return false;
      let index = this.listFiles.data.findIndex(e => {
        if (e.unique == this.file.unique) {
          return e;
        }
      });
      if (arrow == "right") {
        this.transition = "slide-right";
        if (index + 1 >= this.listFiles.data.length) {
          index = 0;
        } else {
          index = index + 1;
        }
      }
      if (arrow == "left") {
        this.transition = "slide-left";
        if (index - 1 <= 0) {
          index = this.listFiles.data.length - 1;
        } else {
          index = index - 1;
        }
      }
      this.file = {};
      this.$axios
        .get("/media/" + this.listFiles.data[index].unique)
        .then(e => {
          setTimeout(() => {
            this.file = e.data;
          }, 300);
        })
        .catch(({ response: { status, statusText, data } }) => {
          if (status == 500) {
            return this.$toast.error(statusText);
          }
          Object.keys(data).forEach(e => {
            this.$toast.error(data[e]);
          });
        });
    },
    deleteAttachment(unique, context) {
      if (!context) {
        this.$refs.deleteLoader.style.visibility = "visible";
      }
      setTimeout(() => {
        this.$axios
          .delete("/media/delete", {
            data: { uniques: unique }
          })
          .then(e => {
            this.fileChoosed.map(e => {
              this.$refs.thumbnail[Object.keys(e)[0]].classList.remove(
                "medma-zs-XAd"
              );
              this.listFiles.data.splice(Object.keys(e)[0], 1);
            });
            this.fileChoosed = [];
            if (!context) {
              this.detail = false;
              this.$refs.deleteLoader.style.visibility = "hidden";
            }
            if (context) {
              this.contextmenu = false;
            }
          })
          .catch(({ response: { status, statusText, data } }) => {
            if (!context) {
              this.$refs.deleteLoader.style.visibility = "hidden";
            }
            if (context) {
              this.contextmenu = false;
            }
            if (status == 500) {
              return this.$toast.error(statusText);
            }
            Object.keys(data).forEach(e => {
              this.$toast.error(data[e]);
            });
          });
      }, 1000);
    },
    setContextMenu(top, left) {
      let context = document.getElementById("contextmenu");
      let contextHeight = this.$refs.contextmenuContent.offsetHeight + 30;
      let contextWidth = context.offsetWidth;
      let windowHeight = window.innerHeight;
      let windowWidth = window.innerWidth;
      let lefts = windowWidth - contextWidth;
      let largestHeight = window.innerHeight - context.offsetHeight - 25;
      let largestWidth = window.innerWidth - context.offsetWidth - 25;
      this.$refs.contextmenu.style.opacity = 1;
      if (windowWidth - left <= contextWidth + 30) {
        left = left - contextWidth;
      }
      if (windowHeight - top <= contextHeight) {
        top = top - contextHeight;
      }
      if (windowHeight - 1 == top + contextHeight) {
        top = top - 20;
      }
      context.style.top = top + "px";
      context.style.left = left + "px";
      context.style.height = contextHeight + "px";
    },
    openContextMenu(i, e, file) {
      let context = document.getElementById("contextmenu");
      context.dataset.unique = file.unique;
      context.dataset.index = i;
      context.style.height = 0;
      this.fileChoosed.length > 1 ? "" : this.selected(i);
      document.addEventListener("mousedown", e => {
        if (!context.contains(e.target) && this.contextmenu) {
          this.contextmenu = false;
        }
      });
      setTimeout(() => {
        this.contextmenu = true;
        Vue.nextTick(
          function() {
            //
            this.setContextMenu(e.y, e.x);
          }.bind(this)
        );
      }, 100);
    },
    closeDetail() {
      this.detail = false;
      this.file = false;
      let body = document.querySelector("body > div[id=app]").parentElement;
      body.style.overflow = "auto";
      this.$refs.medma_detail.style.opacity = 0;
      this.$refs.medma_detail.style.display = "none";
    },
    downloadFile(unique) {
      this.$axios
        .post("/media/d/file/download", {
          uniques: this.fileChoosed
        })
        .then(e => {
          window.location.href = e.data.url;
          this.contextmenu = false;
        });
    },
    filterSearch(value) {
      this.listFiles.data = this.listFiles.data.filter(e => {
        var regex = new RegExp(value, "g");
        return e.name.match(regex);
      });
    }
  },
  filters: {
    getFileSize(size) {
      return size == 0 ? 0 : Math.round(size / 1024);
    }
  },
  mounted() {
    document.addEventListener("keydown", e => {
      if (e.key == "Shift") {
        this.multiple = true;
      }
    });
    document.addEventListener("keyup", e => {
      if (e.key == "Shift") {
        this.multiple = false;
      }
    });
    let infinite = document.getElementsByClassName('infinite-loading-container')[0]
    infinite.removeChild(infinite.children[2]);
},
};
</script>

<style lang="css" scoped></style>
