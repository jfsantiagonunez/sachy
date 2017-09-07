module CategoriesHelper
  
  def findRules(category_id)
    return Rule.where(category_id: category_id)
  end
  
  def findCategories(grupo_id)
    return Category.where(grupo_id: grupo_id)
  end
  
  def createRule(name,category_id)
    return Rule.where(name: name, category_id:category_id).first_or_create()
  end
  
  def applyRule(rule)
    like_rule = "%#{rule.name}%".downcase
    transactions = Transaction.where('lower(description) like ? AND category_id=1', like_rule)
    transactions.each do |transaction|
      transaction.category_id = rule.category_id
      transaction.save
    end
  end
  
  def reapplyAllRules()
    categories = Category.all  
    categories.each do |category|    
      reapplyCategory(category)
    end
  end
  
  def reapplyCategory(category)
    category.rules.each do |rule|
        applyRule(rule)
      end
  end
  
  def untagCategory(category_id)
    default =1
    transactions = Transaction.where(category_id: category_id)
    transactions.each do |transaction|
      transaction.category_id = default
      transaction.save
    end
    reapplyCategory(Category.find(category_id))
  end
  
  def getCategoriesForSelect()
    return Category.all.pluck(:name,:id)
  end
  
  TAG_CATEGORY_GASTO_TYPE = 0
  TAG_CATEGORY_GASTO = "GASTO"
  TAG_CATEGORY_INCOME_TYPE = 1
  TAG_CATEGORY_INCOME = "INGRESO"
  TAG_CATEGORY_AHORRO_TYPE = 2
  TAG_CATEGORY_AHORRO = "AHORRO"
  
  def getCategoryTypes
    return [[TAG_CATEGORY_GASTO,TAG_CATEGORY_GASTO_TYPE],
          [TAG_CATEGORY_INCOME,TAG_CATEGORY_INCOME_TYPE],
          [TAG_CATEGORY_AHORRO,TAG_CATEGORY_AHORRO_TYPE]]
  end
  
  TAG_CATEGORY_TYPES={ TAG_CATEGORY_GASTO_TYPE =>TAG_CATEGORY_GASTO,
                       TAG_CATEGORY_INCOME_TYPE => TAG_CATEGORY_INCOME ,
                       TAG_CATEGORY_AHORRO_TYPE => TAG_CATEGORY_AHORRO}
  def getCategoryTypeName(ctype)
    return TAG_CATEGORY_TYPES[ctype]
  end
  
  def getCategoryHistoryAllAccounts(category)
    months={}
    backInTime = Date.today - 11.months
    @lastMonth=Date.new(backInTime.year, backInTime.month,1)
    @oldMonth=nil
    for i in 1..12  
      theDate = Date.new(backInTime.year, backInTime.month,1)
      months[theDate.strftime('%b %Y')]=theDate
      backInTime += 1.month
    end
    data={}
    vectorAccounts={}
    
    Account.all.each do |account|
      name="#{account.name}-#{account.account_number}"
      data[account.id]={}
      vectorAccounts[account.id]={'name'=>name,'total'=>0}
    end

    months.each do |nameMonth,thisMonthDate|
      datasMonth = Transaction.where('category_id = ? AND transaction_date >= ? AND transaction_date < ?',category.id,thisMonthDate,thisMonthDate + 1.month).group(:account_id).sum(:amount)
      datasMonth.each do |keyAcc,value|
        vectorAccounts[keyAcc]['total']+=value
        data[keyAcc][nameMonth]=value
      end
      if !datasMonth.empty?
        if @lastMonth < thisMonthDate
          @lastMonth = thisMonthDate
        end
        if @oldMonth.nil?
          @oldMonth = thisMonthDate
        elsif thisMonthDate < @oldMonth
          @oldMonth = thisMonthDate
        end
      end
    end 
    dataOut={}
    data.each do |keyAcc,dataMonth|
      dataOut[vectorAccounts[keyAcc]['name']]=data[keyAcc]
      
      if !@oldMonth.nil? 
        numberMonths = ((@lastMonth - @oldMonth).to_i / 30.0).round + 1
        dataOut[vectorAccounts[keyAcc]['name']]["Avg #{numberMonths} m"]=vectorAccounts[keyAcc]['total']/numberMonths
        dataOut[vectorAccounts[keyAcc]['name']]["Total"]=vectorAccounts[keyAcc]['total']
      end
    end
    return dataOut
  end
end
