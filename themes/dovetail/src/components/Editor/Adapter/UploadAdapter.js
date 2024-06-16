export default class UploadAdapter {
  constructor (loader, url) {
    this.loader = loader;
    this.url = url;
  }

  // Starts the upload process.
  upload () {
    return this.loader.file
      .then(file => new Promise((resolve, reject) => {
        this.xhr = axios.post(this.url, this.data(file), {
          headers: {'Content-Type': 'multipart/form-data'}
        }).then(response => {
          resolve(response.data);
        }).catch(err => {
          reject(err);
        });
      }));
  }

  data (file) {
    const data = new FormData();
    data.append('file', file);

    return data;
  }

  // Aborts the upload process.
  abort () {
    //
  }
}
