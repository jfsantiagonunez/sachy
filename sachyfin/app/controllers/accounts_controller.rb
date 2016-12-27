class AccountsController < ApplicationController
  
  include AccountsHelper
  
  before_action :set_account, only: [:show, :edit, :update, :destroy]

  before_action :all_accounts, only: [:index, :create, :update, :destroy,:upload]

  respond_to :html, :js

  # GET /accounts
  # GET /accounts.json


  # GET /accounts/1
  # GET /accounts/1.json
  def show
  end

  # GET /accounts/new
  def new
    @account = Account.new
  end

  # GET /accounts/1/edit
  def edit
  end

  # POST /accounts
  # POST /accounts.json
  def create
    @account = Account.new(account_params)

    respond_to do |format|
      if @account.save
        format.html { redirect_to @account, notice: 'Account was successfully created.' }
        format.json { render :show, status: :created, location: @account }
      else
        format.html { render :new }
        format.json { render json: @account.errors, status: :unprocessable_entity }
      end
    end
  end

  # PATCH/PUT /accounts/1
  # PATCH/PUT /accounts/1.json
  def update
    respond_to do |format|
      if @account.update(account_params)
        format.html { redirect_to @account, notice: 'Account was successfully updated.' }
        format.json { render :show, status: :ok, location: @account }
      else
        format.html { render :edit }
        format.json { render json: @account.errors, status: :unprocessable_entity }
      end
    end
  end

  # DELETE /accounts/1
  # DELETE /accounts/1.json
  def destroy
    @account.destroy
    respond_to do |format|
      format.html { redirect_to accounts_url, notice: 'Account was successfully destroyed.' }
      format.json { head :no_content }
    end
  end

  def upload
    if params[:upload][:file]
      ffile = params[:upload][:file]
      name = params[:upload][:file].original_filename
      begin
        Dir.mkdir(Rails.root.join('public', 'uploads')) unless File.exists?(Rails.root.join('public', 'uploads'))
        fileOnUploadsPath = Rails.root.join('public', 'uploads', name)
        File.open(fileOnUploadsPath, 'wb') do |file|
          file.write(ffile.read)
        end
        importTransactionsFile(fileOnUploadsPath)
        #FileImportJob.perform_async(uploaded_io.original_filename)
        redirect_to accounts_path
      rescue StandardError => ex
        #Rails.logger.debug "#{GlobalConstants::DEBUG_MSG} - RallyImporterController exception: #{ex} - #{ex.message} - #{ex.backtrace.join("\n")}"
        flash[:error] = "Error: Impossible to upload file #{ex} - #{ex.message}"
        redirect_to action: 'index', status: 302
      end
    else
      flash[:error] = "Error: No upload file provided"
      redirect_to action: 'index', status: 302
    end

  end

  def review
    @account = Account.find(params[:id])
    @grupos = Grupo.all
    @categories = getCategoriesForSelect()
  end
  
  def resetcategory
    resetCategory
    redirect_to review_path
  end
  
  private
    # Use callbacks to share common setup or constraints between actions.
    def set_account
      @account = Account.find(params[:id])
    end

    # Never trust parameters from the scary internet, only allow the white list through.
    def account_params
      params.require(:account).permit(:account_number, :name, :last_import, :last_transaction)
    end
    
    def all_accounts
      @accounts = allAccounts()
    end
end
